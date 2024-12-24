import datetime
import logging
import sys
import json
import asyncio

from jeedomdaemon.base_daemon import BaseDaemon
from jeedomdaemon.base_config import BaseConfig

from pyworxcloud import WorxCloud
from pyworxcloud.clouds import CloudType
from pyworxcloud.exceptions import AuthorizationError
from pyworxcloud.events import LandroidEvent
from pyworxcloud.utils.devices import DeviceHandler


class WorxConfig(BaseConfig):
    def __init__(self):
        super().__init__()

        self.add_argument("--email", type=str)
        self.add_argument("--pswd", type=str)

    @property
    def email(self):
        return str(self._args.email)

    @property
    def password(self):
        return str(self._args.pswd)


class worxLandroidS(BaseDaemon):
    def __init__(self) -> None:
        self._config = WorxConfig()
        super().__init__(self._config, self.on_start, self.on_socket_message, self.on_stop)

        self._auto_reconnect_task = None

        self.set_logger_log_level('pyworxcloud')

        self._worxcloud = None

    async def on_start(self):
        self._worxcloud = WorxCloud(self._config.email, self._config.password, CloudType.WORX)
        try:
            self._worxcloud.authenticate()
        except AuthorizationError as e:
            self._logger.error('AuthorizationError: %s', e)
        except Exception as e:
            self._logger.error('Exception during authentication: %s', e)
        else:

            self._worxcloud.connect()
            self._worxcloud.set_callback(LandroidEvent.DATA_RECEIVED, self._on_worx_message)
            await self._send_devices()
            await self._update_all()

            self._auto_reconnect_task = asyncio.create_task(self.__auto_reconnect())

    async def on_stop(self):
        self._auto_reconnect_task.cancel()
        self._worxcloud.disconnect()

    def _get_device_to_send(self, device: DeviceHandler):
        device_to_send = (vars(device)).copy()
        for key in ['_api', '_mower', '_tz', '_DeviceHandler__is_decoded', '_DeviceHandler__raw_data', '_DeviceHandler__json_data', 'capabilities']:
            device_to_send.pop(key, '')
        if self._logger.level > logging.DEBUG:
            device_to_send.pop('last_status', '')
        return device_to_send

    def _on_worx_message(self, name, device: DeviceHandler):
        tmpDevice = self._get_device_to_send(device)
        self._logger.debug("on worx message %s, %s", name, tmpDevice)
        self._loop.create_task(self.__format_and_send('update::' + device.uuid, tmpDevice))

    async def on_socket_message(self, message: list):
        if message['action'] == 'stop':
            await self.stop()
        elif message['action'] == 'synchronize':
            self._worxcloud.fetch()
            await self._send_devices()
        elif message['action'] == 'get_activity_logs':
            device = self._worxcloud.get_device_by_serial_number(message['serial_number'])

            logs = self._worxcloud.get_activity_logs(device)

            payload = [log.__dict__ for log in logs.values()]
            await self.__format_and_send('activity_logs::' + device.uuid, payload)
        else:
            await self._executeAction(message)

    async def __auto_reconnect(self):
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
            self._logger.info("stop auto refresh token")

    async def _send_devices(self):
        await self.__format_and_send('devices', [self._get_device_to_send(d) for d in self._worxcloud.devices.values()])

    async def _update_all(self):
        device: DeviceHandler
        for device in self._worxcloud.devices.values():
            # self._logger.debug("update device %s", vars(device))
            self._worxcloud.update(device.serial_number)

    async def _executeAction(self, message: list):
        action: str = message['action']
        self._logger.info('Execution of action %s', action)
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
            self._logger.error('Error during execute action: %s(%s) in %s on line %s', e, exception_type, filename, line_number)

    def __methodNotFound(self, *_):
        self._logger.error('unknown method')

    def __encoder(self, obj):
        try:
            if isinstance(obj, (datetime.date, datetime.datetime)):
                return obj.isoformat()
            if isinstance(obj, type(None)):
                return None
            return obj.__dict__
        except Exception as e:
            self._logger.warning('error encoding %s : %s', str(obj), e)
        return ''

    async def __format_and_send(self, key, data):
        payload = json.loads(json.dumps(data, default=lambda d: self.__encoder(d)))
        await self.add_change(key, payload)


worxLandroidS().run()
