import datetime
import logging
import argparse
import sys
import os
import signal
import json
import asyncio
import aiohttp

from config import Config
from jeedom.jeedom import jeedom_utils, jeedom_socket, JEEDOM_SOCKET_MESSAGE

from pyworxcloud import WorxCloud
from pyworxcloud.clouds import CloudType
from pyworxcloud.exceptions import AuthorizationError
from pyworxcloud.events import LandroidEvent
from pyworxcloud.utils.devices import DeviceHandler


class worxLandroidS:
    def __init__(self, config: Config) -> None:
        self._config = config
        self._listen_task = None
        self._auto_reconnect_task = None
        self._jeedom_session = None
        self._loop = None
        self._jeedomSocket = jeedom_socket(port=self._config.socketport, address='localhost')

        self._cloud = None

    async def main(self):
        self._jeedom_session = aiohttp.ClientSession()
        if not await self.__test_callback():
            await self._jeedom_session.close()
            return

        self._cloud = WorxCloud(self._config.email, self._config.password, CloudType.WORX)
        auth = False
        try:
            auth = self._cloud.authenticate()
        except AuthorizationError as e:
            _LOGGER.error('AuthorizationError: %s', e)
        except Exception as e:
            _LOGGER.error('Exception during authentication: %s', e)

        if not auth:
            await self._jeedom_session.close()
            return

        self._cloud.connect()
        self._cloud.set_callback(LandroidEvent.DATA_RECEIVED, self._on_message)
        await self._send_devices()
        await self._update_all()

        self._loop = asyncio.get_event_loop()

        self._listen_task = asyncio.create_task(self._listen())
        self._auto_reconnect_task = asyncio.create_task(self._auto_reconnect())
        await asyncio.gather(self._auto_reconnect_task, self._listen_task)

        await self._jeedom_session.close()

    def close(self):
        self._auto_reconnect_task.cancel()
        self._listen_task.cancel()
        self._cloud.disconnect()

    def _get_device_to_send(self, device: DeviceHandler):
        device_to_send = (vars(device)).copy()
        for key in ['_api', '_mower', '_tz', '_DeviceHandler__is_decoded', '_DeviceHandler__raw_data', '_DeviceHandler__json_data']:
            device_to_send.pop(key, '')
        return device_to_send

    def _on_message(self, name, device: DeviceHandler):
        tmpDevice = self._get_device_to_send(device)
        _LOGGER.debug("on message %s, %s", name, str(tmpDevice))
        tmp = {}
        tmp["uuid"] = device.uuid
        tmp["data"] = tmpDevice
        self._loop.create_task(self.__send_async(tmp))

    async def _read_socket(self):
        global JEEDOM_SOCKET_MESSAGE
        if not JEEDOM_SOCKET_MESSAGE.empty():
            _LOGGER.debug("Message received in socket JEEDOM_SOCKET_MESSAGE")
            message = json.loads(JEEDOM_SOCKET_MESSAGE.get().decode('utf-8'))
            if message['apikey'] != _apikey:
                _LOGGER.error('Invalid apikey from socket : %s', str(message))
                return
            try:
                if message['action'] == 'stop':
                    self.close()
                elif message['action'] == 'synchronize':
                    await self._send_devices()
                elif message['action'] == 'get_activity_logs':
                    device = self._cloud.get_device_by_serial_number(message['serial_number'])
                    tmp = {
                        "activity_logs": device.uuid,
                        "data": self._cloud.get_activity_logs(device.serial_number)
                    }
                    await self.__send_async(tmp)
                else:
                    await self._executeAction(message)
            except Exception as e:
                _LOGGER.error('Send command to daemon error: %s', e)

    async def _auto_reconnect(self):
        try:
            while True:
                await asyncio.sleep(self._cloud.get_token_expires_in() * 0.8)
                success = self._cloud.renew_connection()
                retry = 0
                while not success and retry < 12:
                    await asyncio.sleep(60)
                    retry += 1
                    success = self._cloud.renew_connection()
                if not success:
                    raise Exception("Impossible to renew token, issue with cloud server")
                else:
                    await self._update_all()
        except asyncio.CancelledError:
            _LOGGER.info("stop auto refresh token")

    async def _send_devices(self):
        tmp = {}
        tmp["devices"] = [self._get_device_to_send(d) for d in self._cloud.devices.values()]
        await self.__send_async(tmp)

    async def _update_all(self):
        for device in self._cloud.devices.values():
            # _LOGGER.debug("update device %s", vars(device))
            self._cloud.update(device.serial_number)

    async def _executeAction(self, message):
        action: str = message['action']
        _LOGGER.info('Execution of action %s', action)
        worx_method = getattr(self._cloud, action, self.__methodNotFound)

        try:
            if 'args' in message:
                worx_method(message['serial_number'], *message['args'])
            else:
                worx_method(message['serial_number'])
        except Exception as e:
            _LOGGER.error('Error during execute action: %s', e)

    def __methodNotFound(*_):
        _LOGGER.error('unknown method')

    async def _listen(self):
        _LOGGER.info("Start listening")
        self._jeedomSocket.open()
        try:
            while 1:
                await asyncio.sleep(0.05)
                await self._read_socket()
        except KeyboardInterrupt:
            _LOGGER.info("End listening")
            shutdown()
        except asyncio.CancelledError:
            _LOGGER.info("listening cancelled")

    async def __test_callback(self):
        try:
            async with self._jeedom_session.get(self._config.callbackUrl + '?test=1&apikey=' + self._config.apiKey) as resp:
                if resp.status != 200:
                    _LOGGER.error("Please check your network configuration page: %s-%s", resp.status, resp.reason)
                    return False
        except Exception as e:
            _LOGGER.error('Callback error: %s. Please check your network configuration page', e)
            return False
        return True

    def __encoder(self, obj):
        try:
            if isinstance(obj, (datetime.date, datetime.datetime)):
                return obj.isoformat()
            if isinstance(obj, type(None)):
                return None
            return obj.__dict__
        except Exception as e:
            _LOGGER.warning('error encoding %s : %s', str(obj), e)
        return ''

    async def __send_async(self, data):

        _LOGGER.debug('Send to jeedom :  %s', data)
        payload = json.loads(json.dumps(data, default=lambda d: self.__encoder(d)))
        async with self._jeedom_session.post(self._config.callbackUrl + '?apikey=' + self._config.apiKey, json=payload) as resp:
            if resp.status != 200:
                _LOGGER.error('Error on send request to jeedom, return %s-%s', resp.status, resp.reason)

# ----------------------------------------------------------------------------


def handler(signum=None, frame=None):
    _LOGGER.debug("Signal %i caught, exiting..." % int(signum))
    worx.close()


def shutdown():
    _LOGGER.info("Shuting down")

    try:
        _LOGGER.debug("Removing PID file " + str(_pidfile))
        os.remove(_pidfile)
    except:
        pass

    _LOGGER.debug("Exit 0")
    sys.stdout.flush()
    os._exit(0)

# ----------------------------------------------------------------------------


_log_level = "error"
_pidfile = '/tmp/worxLandroidSd.pid'
_apikey = ''
_LOGGER = logging.getLogger(__name__)

parser = argparse.ArgumentParser(description='worxLandroidS Daemon for Jeedom plugin')
parser.add_argument("--loglevel", help="Log Level for the daemon", type=str)
parser.add_argument("--email", help="worx username", type=str)
parser.add_argument("--pswd", help="worx password", type=str)
parser.add_argument("--socketport", help="Socket Port", type=int)
parser.add_argument("--callback", help="Jeedom callback url", type=str)
parser.add_argument("--apikey", help="Plugin API Key", type=str)
parser.add_argument("--pid", help="daemon pid", type=str)

args = parser.parse_args()

_log_level = args.loglevel
_pidfile = args.pid
_apikey = args.apikey

# jeedom_utils.set_log_level(_log_level)
_LOGGER.setLevel(jeedom_utils.convert_log_level(_log_level))
logging.getLogger('pyworxcloud').setLevel(jeedom_utils.convert_log_level(_log_level))

signal.signal(signal.SIGINT, handler)
signal.signal(signal.SIGTERM, handler)

try:
    _LOGGER.info('Starting daemon')
    config = Config(**vars(args))

    _LOGGER.info('Log level: %s', _log_level)
    _LOGGER.debug('Socket port : %s', config.socketport)
    _LOGGER.debug('PID file : '+str(_pidfile))
    jeedom_utils.write_pid(str(_pidfile))

    worx = worxLandroidS(config)
    asyncio.get_event_loop().run_until_complete(worx.main())
except Exception as e:
    _LOGGER.error('Fatal error: %s', e)
shutdown()
