class Config(object):
    def __init__(self, **kwargs):
        self._kwargs = kwargs

    @property
    def email(self):
        return self._kwargs.get('email', '')

    @property
    def password(self):
        return self._kwargs.get('pswd', '')

    @property
    def apiKey(self):
        return self._kwargs.get('apikey', '')

    @property
    def callbackUrl(self):
        return self._kwargs.get('callback', '')

    @property
    def socketport(self):
        return self._kwargs.get('socketport', 55073)
