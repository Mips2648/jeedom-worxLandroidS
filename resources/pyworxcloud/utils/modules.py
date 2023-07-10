"""Location information."""
from __future__ import annotations

from .landroid_class import LDict


class Modules(LDict):
    """Modules."""

    def __init__(self, data) -> dict:
        """Initialize modules object."""
        super().__init__()

        try:
            self["ultrasonic"] = bool(data["last_status"]["payload"]["cfg"]["modules"]["US"]["enabled"])
        except:
            pass
        else:
            self["ultrasonic"] = False

        try:
            self["digital_fence"] = bool(data["last_status"]["payload"]["cfg"]["modules"]["DF"]["fh"])
        except:
            pass
        else:
            self["digital_fence"] = False

        try:
            self["cellular"] = bool(data["last_status"]["payload"]["cfg"]["modules"]["4G"]["enabled"])
        except:
            pass
        else:
            self["cellular"] = False

    @property
    def ultrasonic(self) -> bool:
        """Return ultrasonic."""
        return self["ultrasonic"]

    @ultrasonic.setter
    def ultrasonic(self, enabled: bool) -> None:
        """Set ultrasonic information."""
        self["ultrasonic"] = enabled

    @property
    def digital_fence(self) -> bool:
        """Return digital_fence."""
        return self["digital_fence"]

    @digital_fence.setter
    def digital_fence(self, enabled: bool) -> None:
        """Set digital_fence information."""
        self["digital_fence"] = enabled

    @property
    def cellular(self) -> bool:
        """Return ultrasonic."""
        return self["cellular"]

    @cellular.setter
    def cellular(self, enabled: bool) -> None:
        """Set ultrasonic information."""
        self["cellular"] = enabled
