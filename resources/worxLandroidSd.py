import datetime
import logging
import argparse
import sys
import os
import signal
import json
import asyncio
import functools

from config import Config
from jeedom.utils import Utils
from jeedom.aio_connector import Listener, Publisher

from pyworxcloud import WorxCloud
from pyworxcloud.clouds import CloudType
from pyworxcloud.exceptions import AuthorizationError
from pyworxcloud.events import LandroidEvent
from pyworxcloud.utils.devices import DeviceHandler

class worxLandroidS:
    def __init__(self, config: Config) -> None:
        self._config = config
        self._jeedom_publisher = None
        self._listen_task = None
        self._auto_reconnect_task = None
        self._loop = None
        self._logger = logging.getLogger(__name__)

        self._worxcloud = None

    async def main(self):
        self._jeedom_publisher = Publisher(self._config.callback_url, self._config.api_key, self._config.cycle)
        if not await self._jeedom_publisher.test_callback():
            return

        self._loop = asyncio.get_running_loop()

        self._worxcloud = WorxCloud(self._config.email, self._config.password, CloudType.WORX)
        try:
            self._worxcloud.authenticate()
        except AuthorizationError as e:
            _LOGGER.error('AuthorizationError: %s', e)
        except Exception as e:
            _LOGGER.error('Exception during authentication: %s', e)
        else:
            await self.add_signal_handler()

            self._worxcloud.connect()
            self._worxcloud.set_callback(LandroidEvent.DATA_RECEIVED, self._on_message)
            await self._send_devices()
            await self._update_all()

            self._listen_task = Listener.create_listen_task(self._config.socket_host, self._config.socket_port, self._on_socket_message)
            self._auto_reconnect_task = asyncio.create_task(self._auto_reconnect())
            await asyncio.sleep(1) # allow  all tasks to start
            self._logger.info("Ready")
            await asyncio.gather(self._auto_reconnect_task, self._listen_task)

    async def add_signal_handler(self):
        self._loop.add_signal_handler(signal.SIGINT, functools.partial(self._ask_exit, signal.SIGINT))
        self._loop.add_signal_handler(signal.SIGTERM, functools.partial(self._ask_exit, signal.SIGTERM))

    def _ask_exit(self, sig):
        self._logger.info("Signal %i caught, exiting...", sig)
        self.close()

    def close(self):
        self._auto_reconnect_task.cancel()
        self._listen_task.cancel()
        self._worxcloud.disconnect()

    def _get_device_to_send(self, device: DeviceHandler):
        device_to_send = (vars(device)).copy()
        for key in ['_api', '_mower', '_tz', '_DeviceHandler__is_decoded', '_DeviceHandler__raw_data', '_DeviceHandler__json_data', 'capabilities']:
            device_to_send.pop(key, '')
        if _LOGGER.level > logging.DEBUG:
            device_to_send.pop('last_status', '')
        return device_to_send

    def _on_message(self, name, device: DeviceHandler):
        tmpDevice = self._get_device_to_send(device)
        _LOGGER.debug("on message %s, %s", name, str(tmpDevice))
        tmp = {}
        tmp["uuid"] = device.uuid
        tmp["data"] = tmpDevice
        self._loop.create_task(self.__send_async(tmp))


    async def _on_socket_message(self, message):
        if message['apikey'] != self._config.api_key:
            _LOGGER.error('Invalid apikey from socket : %s', str(message))
            return
        try:
            if message['action'] == 'stop':
                self.close()
            elif message['action'] == 'synchronize':
                self._worxcloud.fetch()
                await self._send_devices()
            elif message['action'] == 'get_activity_logs':
                device = self._worxcloud.get_device_by_serial_number(message['serial_number'])

                logs = self._worxcloud.get_activity_logs(device)

                tmp = {
                    "activity_logs": device.uuid,
                    "data": [l.__dict__ for l in logs.values()]
                }
                await self.__send_async(tmp)
            else:
                await self._executeAction(message)
        except Exception as e:
            _LOGGER.error('Send command to daemon error: %s', e)

    async def _auto_reconnect(self):
        try:
            while True:
                await asyncio.sleep(self._worxcloud.get_token_expires_in() * 0.9)
                success = self._worxcloud.renew_connection()
                retry = 0
                while not success and retry < 12:
                    await asyncio.sleep(60)
                    retry += 1
                    success = self._worxcloud.renew_connection()
                if not success:
                    raise Exception("Impossible to renew token, issue with cloud server")
                else:
                    await self._update_all()
        except asyncio.CancelledError:
            _LOGGER.info("stop auto refresh token")

    async def _send_devices(self):
        tmp = {}
        tmp["devices"] = [self._get_device_to_send(d) for d in self._worxcloud.devices.values()]
        await self.__send_async(tmp)

    async def _update_all(self):
        for device in self._worxcloud.devices.values():
            # _LOGGER.debug("update device %s", vars(device))
            self._worxcloud.update(device.serial_number)

    async def _executeAction(self, message):
        action: str = message['action']
        _LOGGER.info('Execution of action %s', action)
        worx_method = getattr(self._worxcloud, action, self.__methodNotFound)

        try:
            if 'args' in message:
                worx_method(message['serial_number'], *message['args'])
            else:
                worx_method(message['serial_number'])
        except Exception as e:
            exception_type, exception_object, exception_traceback = sys.exc_info()
            filename = exception_traceback.tb_frame.f_code.co_filename
            line_number = exception_traceback.tb_lineno
            _LOGGER.error('Error during execute action: %s(%s) in %s on line %s', e, exception_type, filename, line_number)

    def __methodNotFound(*_):
        _LOGGER.error('unknown method')

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
        payload = json.loads(json.dumps(data, default=lambda d: self.__encoder(d)))
        await self._jeedom_publisher.send_to_jeedom(payload)

# ----------------------------------------------------------------------------

def shutdown():
    _LOGGER.info("Shuting down")
    try:
        _LOGGER.debug("Removing PID file %s", config.pid_filename)
        os.remove(config.pid_filename)
    except:
        pass

    _LOGGER.debug("Exit 0")
    sys.stdout.flush()
    os._exit(0)

# ----------------------------------------------------------------------------

parser = argparse.ArgumentParser(description='worxLandroidS Daemon for Jeedom plugin')
parser.add_argument("--loglevel", help="Log Level for the daemon", type=str)
parser.add_argument("--email", help="worx username", type=str)
parser.add_argument("--pswd", help="worx password", type=str)
parser.add_argument("--socketport", help="Socket Port", type=int)
parser.add_argument("--callback", help="Jeedom callback url", type=str)
parser.add_argument("--apikey", help="Plugin API Key", type=str)
parser.add_argument("--pid", help="daemon pid", type=str)

args = parser.parse_args()
config = Config(**vars(args))

Utils.init_logger(config.log_level)
_LOGGER = logging.getLogger(__name__)
logging.getLogger('asyncio').setLevel(logging.WARNING)
logging.getLogger('pyworxcloud').setLevel(Utils.convert_log_level(config.log_level))

try:
    _LOGGER.info('Starting daemon')
    _LOGGER.info('Log level: %s', config.log_level)
    Utils.write_pid(str(config.pid_filename))

    worx = worxLandroidS(config)
    asyncio.run(worx.main())
except Exception as e:
    exception_type, exception_object, exception_traceback = sys.exc_info()
    filename = exception_traceback.tb_frame.f_code.co_filename
    line_number = exception_traceback.tb_lineno
    _LOGGER.error('Fatal error: %s(%s) in %s on line %s', e, exception_type, filename, line_number)
shutdown()
