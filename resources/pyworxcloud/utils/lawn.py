"""Handling lawn parameters."""
from __future__ import annotations

from .landroid_class import LDict


class Lawn(LDict):
    """Handler for lawn parameters."""

    def __init__(self, data) -> None:
        super().__init__()

        self["perimeter"] = data["lawn_perimeter"] if "lawn_perimeter" in data else 0
        self["size"] = data["lawn_size"] if "lawn_size" in data else 0
