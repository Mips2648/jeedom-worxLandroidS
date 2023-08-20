"""Defines schedule classes."""
from __future__ import annotations

import calendar
from datetime import datetime, timedelta
from enum import IntEnum
import logging
from backports.zoneinfo import ZoneInfo

from ..const import CONST_UNKNOWN
from ..day_map import DAY_MAP
from .landroid_class import LDict


class ScheduleType(IntEnum):
    """Schedule types."""

    PRIMARY = 0
    SECONDARY = 1


TYPE_TO_STRING = {ScheduleType.PRIMARY: "primary", ScheduleType.SECONDARY: "secondary"}

_LOGGER = logging.getLogger(__name__)


class WeekdaySettings(LDict):
    """Class representing a weekday setting."""

    def __init__(
        self,
        start: str = "00:00",
        end: str = "00:00",
        duration: int = 0,
        boundary: bool = False,
    ) -> None:
        """Initialize the settings."""
        super().__init__()
        self["start"] = start
        self["end"] = end
        self["duration"] = duration
        self["boundary"] = boundary


class Weekdays(LDict):
    """Represents all weekdays."""

    def __init__(self) -> dict:
        super().__init__()

        for day in list(calendar.day_name):
            self.update({day.lower(): WeekdaySettings()})


class ScheduleInfo:
    """Used for calculate the current schedule progress and show next schedule start."""

    def __init__(self, schedule: Schedule, tz: str | None = None) -> None:
        """Initialize the ScheduleInfo object and set values."""
        self.__schedule = schedule
        now = datetime.now()
        timezone = ZoneInfo(tz) if not isinstance(tz, type(None)) else ZoneInfo("UTC")
        self._tz = tz
        self.__now = now.astimezone(timezone)
        self.__today = self.__now.strftime("%d/%m/%Y")

    def _get_schedules(
        self, date: datetime, next: bool = False, add_offset: bool = False
    ) -> WeekdaySettings | None:
        """Get primary and secondary schedule for today or tomorrow."""
        day = DAY_MAP[int(date.strftime("%w"))]

        if TYPE_TO_STRING[ScheduleType.PRIMARY] in self.__schedule:
            primary = self.__schedule[TYPE_TO_STRING[ScheduleType.PRIMARY]][day]
            if primary["duration"] == 0:
                return None, None, date
        else:
            return None, None, date

        if TYPE_TO_STRING[ScheduleType.SECONDARY] in self.__schedule:
            secondary = self.__schedule[TYPE_TO_STRING[ScheduleType.SECONDARY]][day]
            if secondary["duration"] == 0:
                secondary = None
        else:
            secondary = None

        return primary, secondary, date

    def calculate_progress(self) -> int:
        """Calculate and return current progress in percent."""
        from ..helpers.time_format import string_to_time

        primary, secondary, date = self._get_schedules(self.__now)

        if isinstance(primary, type(None)) and isinstance(secondary, type(None)):
            return 100

        start = string_to_time(f"{self.__today} {primary['start']}:00", self._tz)
        end = string_to_time(f"{self.__today} {primary['end']}:00", self._tz)

        total_run = primary["duration"]
        has_run = 0

        if self.__now >= start and self.__now < end:
            has_run = (self.__now - start).total_seconds() / 60
        elif self.__now < start:
            has_run = 0
        else:
            has_run = primary["duration"]

        if (not isinstance(secondary, type(None))) and secondary["duration"] > 0:
            start = string_to_time(f"{self.__today} {secondary['start']}:00", self._tz)
            end = string_to_time(f"{self.__today} {secondary['end']}:00", self._tz)
            total_run += secondary["duration"]
            if self.__now >= start and self.__now < end:
                has_run += (self.__now - start).total_seconds() / 60
            elif self.__now < start:
                has_run += 0
            else:
                has_run += primary["duration"]

        pct = (has_run / total_run) * 100
        return int(round(pct))

    def next_schedule(self) -> str:
        """Find next schedule starting point."""
        from ..helpers.time_format import string_to_time

        primary, secondary, date = self._get_schedules(self.__now)
        next = None
        cnt = 0
        while isinstance(primary, type(None)):
            if cnt == 7:
                # No schedule active for any weekday, return None
                return None

            primary, secondary, date = self._get_schedules(date + timedelta(days=1))
            cnt += 1

        start = string_to_time(
            f"{date.strftime('%d/%m/%Y')} {primary['start']}:00", self._tz
        )

        if self.__now < start:
            next = start
        elif (
            (not isinstance(secondary, type(None)))
            and start
            < string_to_time(
                f"{date.strftime('%d/%m/%Y')} {secondary['start']}:00", self._tz
            )
            and secondary["duration"] > 0
        ):
            next = string_to_time(
                f"{date.strftime('%d/%m/%Y')} {secondary['start']}:00", self._tz
            )

        if isinstance(next, type(None)):
            primary, secondary, date = self._get_schedules(date + timedelta(days=1))
            while isinstance(primary, type(None)):
                primary, secondary, date = self._get_schedules(date + timedelta(days=1))
            next = string_to_time(
                f"{date.strftime('%d/%m/%Y')} {primary['start']}:00", self._tz
            )

        return next.strftime("%Y-%m-%d %H:%M:%S")


class Schedule(LDict):
    """Represents a schedule."""

    def __init__(self, data) -> None:
        """Initialize a schedule."""
        super().__init__()

        try:
            self["daily_progress"] = None
            self["next_schedule_start"] = None
            self["time_extension"] = 0
            self["active"] = True
            self["auto_schedule"] = {
                "settings": AutoScheduleSettings(data["auto_schedule_settings"]) if "auto_schedule_settings" in data else {},
                "enabled": data["auto_schedule"] if "auto_schedule" in data else False,
            }
        except Exception as e:
            _LOGGER.error('Exception during init Schedule: %s', e)

    def update_progress_and_next(self, tz: str | None = None) -> None:
        """Update progress and next scheduled start properties."""

        info = ScheduleInfo(self, tz)
        self["daily_progress"] = info.calculate_progress()
        self["next_schedule_start"] = info.next_schedule()


class AutoScheduleSettings(LDict):
    """Handler for AutoScheduleSettings parameters."""

    def __init__(self, data) -> None:
        super().__init__()

        from ..helpers.time_format import minute_to_hour

        if data is None:
            self["boost"] = 0
            self["grass_type"] = CONST_UNKNOWN
            self["irrigation"] = False
            self["nutrition"] = None
            self["soil_type"] = CONST_UNKNOWN
            self["exclusion_scheduler"] = {}
            return

        try:
            self["boost"] = data["boost"] if "boost" in data else 0
            self["grass_type"] = data["grass_type"] if "grass_type" in data else CONST_UNKNOWN
            self["irrigation"] = data["irrigation"] if "irrigation" in data else False
            self["nutrition"] = data["nutrition"] if "nutrition" in data else None
            self["soil_type"] = data["soil_type"] if "soil_type" in data else CONST_UNKNOWN
            self["exclusion_scheduler"] = data["exclusion_scheduler"] if "exclusion_scheduler" in data else {}

            if "days" in self["exclusion_scheduler"]:
                for day in self["exclusion_scheduler"]["days"]:
                    for slot in day["slots"]:
                        slot["end_time"] = minute_to_hour(slot["start_time"] + slot["duration"])
                        slot["start_time"] = minute_to_hour(slot["start_time"])
        except Exception as e:
            _LOGGER.error('Exception during init AutoScheduleSettings: %s', e)
