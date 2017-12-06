# nette-rollbar

Usage:

config.neon:

```neon
parameters:
    rollbar:
        key: a1b2c3d4e5f6a7b8c9d0a1b2c3d4e5f6
        env: dev

services:    
    tracy.logger:
        class: Mlezitom\NetteRollbar\Logger(%rollbar.key%, %rollbar.env%, @security.user::getIdentity())

```