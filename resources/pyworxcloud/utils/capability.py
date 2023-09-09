"""Device capabilities."""
from __future__ import annotations

import logging
from enum import IntEnum
from typing import Any

_LOGGER = logging.getLogger(__name__)


class DeviceCapability(IntEnum):
    """Available device capabilities."""

    EDGE_CUT = 1
    ONE_TIME_SCHEDULE = 2
    PARTY_MODE = 4
    TORQUE = 8
    ULTRASONIC = 16
    DIGITAL_FENCE = 32
    CELLULAR = 64
    HEADLIGHT = 128


CAPABILITY_TO_TEXT = {
    DeviceCapability.EDGE_CUT: "Edge Cut",
    DeviceCapability.ONE_TIME_SCHEDULE: "One-Time-Schedule",
    DeviceCapability.PARTY_MODE: "Party Mode",
    DeviceCapability.TORQUE: "Motor Torque",
    DeviceCapability.ULTRASONIC: "Anti Collision System",
    DeviceCapability.DIGITAL_FENCE: "Digital Fence",
    DeviceCapability.CELLULAR: "Cellular",
    DeviceCapability.HEADLIGHT: "Headlight"
}

MODULE_KEY = {
    DeviceCapability.ULTRASONIC: "US",
    DeviceCapability.DIGITAL_FENCE: "DF",
    DeviceCapability.CELLULAR: "4G",
    DeviceCapability.HEADLIGHT: "HL"
}


class Capability:
    """Class for handling device capabilities."""

    def __init__(self, device_data: Any | None = None) -> int:
        """Initialize the capability list."""
        # super().__init__()
        self.__int__: int = 0
        self.ready: bool = False

        try:
            if "sc" in device_data["last_status"]["payload"]["cfg"]:
                if "ots" in device_data["last_status"]["payload"]["cfg"]["sc"]:
                    self.add(DeviceCapability.ONE_TIME_SCHEDULE)
                    self.add(DeviceCapability.EDGE_CUT)
                if "distm" in device_data["last_status"]["payload"]["cfg"]["sc"]:
                    self.add(DeviceCapability.PARTY_MODE)
        except TypeError:
            pass

        try:
            if "tq" in device_data["last_status"]["payload"]["cfg"]:
                self.add(DeviceCapability.TORQUE)
        except TypeError:
            pass

        try:
            if "modules" in device_data["last_status"]["payload"]["cfg"]:
                if "US" in device_data["last_status"]["payload"]["cfg"]["modules"]:
                    self.add(DeviceCapability.ULTRASONIC)
                if "DF" in device_data["last_status"]["payload"]["cfg"]["modules"]:
                    self.add(DeviceCapability.DIGITAL_FENCE)
                if "HL" in device_data["last_status"]["payload"]["cfg"]["modules"]:
                    self.add(DeviceCapability.HEADLIGHT)
                if "4G" in device_data["last_status"]["payload"]["cfg"]["modules"]:
                    self.add(DeviceCapability.CELLULAR)
        except TypeError:
            pass

    def add(self, capability: DeviceCapability) -> None:
        """Add capability to the list."""
        if capability & self.__int__ == 0:
            self.__int__ = self.__int__ | capability

    def check(self, capability: DeviceCapability) -> bool:
        """Check if device has capability."""
        if capability & self.__int__ == 0:
            return False
        else:
            return True
