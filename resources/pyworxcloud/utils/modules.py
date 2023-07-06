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

    @property
    def ultrasonic(self) -> bool:
        """Return ultrasonic."""
        return self["ultrasonic"]

    @ultrasonic.setter
    def ultrasonic(self, enabled: bool) -> None:
        """Set ultrasonic information."""
        self["ultrasonic"] = enabled
