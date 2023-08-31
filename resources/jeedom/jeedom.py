# This file is part of Jeedom.
#
# Jeedom is free software: you can redistribute it and/or modify
# it under the terms of the GNU General Public License as published by
# the Free Software Foundation, either version 3 of the License, or
# (at your option) any later version.
#
# Jeedom is distributed in the hope that it will be useful,
# but WITHOUT ANY WARRANTY; without even the implied warranty of
# MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
# GNU General Public License for more details.
#
# You should have received a copy of the GNU General Public License
# along with Jeedom. If not, see <http://www.gnu.org/licenses/>.
#

import logging
import threading
import os
from queue import Queue
import socketserver
from socketserver import (TCPServer, StreamRequestHandler)

# ------------------------------------------------------------------------------


class jeedom_utils():

    @staticmethod
    def convert_log_level(level='error'):
        LEVELS = {'debug': logging.DEBUG,
                  'info': logging.INFO,
                  'notice': logging.WARNING,
                  'warning': logging.WARNING,
                  'error': logging.ERROR,
                  'critical': logging.CRITICAL,
                  'none': logging.NOTSET}
        return LEVELS.get(level, logging.NOTSET)

    @staticmethod
    def init_logger(level='error'):
        FORMAT = '[%(asctime)-15s][%(levelname)s] : %(message)s'
        logging.basicConfig(level=jeedom_utils.convert_log_level(level), format=FORMAT, datefmt="%Y-%m-%d %H:%M:%S")

    @staticmethod
    def stripped(str):
        return "".join([i for i in str if ord(i) in range(32, 127)])

    @staticmethod
    def ByteToHex(byteStr):
        return ''.join(["%02X " % ord(x) for x in str(byteStr)]).strip()

    @staticmethod
    def dec2hex(dec):
        if dec is None:
            return 0
        return hex(dec)[2:]

    @staticmethod
    def testBit(int_type, offset):
        mask = 1 << offset
        return(int_type & mask)

    @staticmethod
    def clearBit(int_type, offset):
        mask = ~(1 << offset)
        return(int_type & mask)

    @staticmethod
    def split_len(seq, length):
        return [seq[i:i+length] for i in range(0, len(seq), length)]

    @staticmethod
    def write_pid(path):
        pid = str(os.getpid())
        logging.debug("Writing PID %s to %s", pid, path)
        open(path, 'w').write("%s\n" % pid)

# ------------------------------------------------------------------------------


JEEDOM_SOCKET_MESSAGE = Queue()


class jeedom_socket_handler(StreamRequestHandler):
    def handle(self):
        global JEEDOM_SOCKET_MESSAGE
        logging.debug("Client connected to [%s:%d]", self.client_address[0], self.client_address[1])
        lg = self.rfile.readline()
        JEEDOM_SOCKET_MESSAGE.put(lg)
        logging.debug("Message read from socket: %s", lg.strip())
        self.netAdapterClientConnected = False
        logging.debug("Client disconnected from [%s:%d]", self.client_address[0], self.client_address[1])


class jeedom_socket():

    def __init__(self, address='localhost', port=55000):
        self.address = address
        self.port = port
        socketserver.TCPServer.allow_reuse_address = True

    def open(self):
        self.netAdapter = TCPServer((self.address, self.port), jeedom_socket_handler)
        if self.netAdapter:
            logging.debug("Socket interface started")
            threading.Thread(target=self.loopNetServer, args=()).start()
        else:
            logging.debug("Cannot start socket interface")

    def loopNetServer(self):
        logging.debug("LoopNetServer Thread started")
        logging.debug("Listening on: [%s:%d]", self.address, self.port)
        self.netAdapter.serve_forever()
        logging.debug("LoopNetServer Thread stopped")

    def close(self):
        self.netAdapter.shutdown()

# ------------------------------------------------------------------------------
# END
# ------------------------------------------------------------------------------
