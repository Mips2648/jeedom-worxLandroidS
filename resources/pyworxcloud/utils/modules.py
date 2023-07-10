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
            self["digital_fence_fh"] = bool(data["last_status"]["payload"]["cfg"]["modules"]["DF"]["fh"])
            self["digital_fence_cut"] = bool(data["last_status"]["payload"]["cfg"]["modules"]["DF"]["cut"])
        except:
            pass
        else:
            self["digital_fence_fh"] = False
            self["digital_fence_cut"] = False

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
    def digital_fence_fh(self) -> bool:
        """Return digital_fence_fh."""
        return self["digital_fence_fh"]

    @digital_fence_fh.setter
    def digital_fence_fh(self, enabled: bool) -> None:
        """Set digital_fence_fh information."""
        self["digital_fence_fh"] = enabled

    @property
    def digital_fence_cut(self) -> bool:
        """Return digital_fence_cut."""
        return self["digital_fence_cut"]

    @digital_fence_cut.setter
    def digital_fence_cut(self, enabled: bool) -> None:
        """Set digital_fence_cut information."""
        self["digital_fence_cut"] = enabled

    @property
    def cellular(self) -> bool:
        """Return ultrasonic."""
        return self["cellular"]

    @cellular.setter
    def cellular(self, enabled: bool) -> None:
        """Set ultrasonic information."""
        self["cellular"] = enabled
