"""Class for handling device info and states."""
from __future__ import annotations
import logging

from .battery import Battery
from .landroid_class import LDict

from .state import States, StateType
from .zone import Zone

_LOGGER = logging.getLogger(__name__)


class Activity(LDict):
    """DeviceHandler for Landroid Cloud devices."""

    def __init__(self, data) -> dict:
        """Initialize the object."""
        super().__init__()

        self._payload = data['payload']

        self.id = data['_id']
        self.updated = self._payload["cfg"]["dt"] + " " + self._payload["cfg"]["tm"]

        self.battery = Battery(self._payload["dat"]["bt"])
        self.error = States(StateType.ERROR)
        self.status = States()
        self.zone = Zone(self._payload)

        self.status.update(self._payload["dat"]["ls"])
        self.error.update(self._payload["dat"]["le"])

        self.locked = bool(self._payload["dat"]["lk"])
