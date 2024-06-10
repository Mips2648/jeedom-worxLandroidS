from jeedomdaemon.base_config import BaseConfig

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