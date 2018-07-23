# Guard

<p align="center">
  <img src="https://raw.githubusercontent.com/lmorel3/guard/master/assets/screenshot.png">
</p>

**Guard** is an open-source _**simple and lightweight**_ SSO authentication handler for reverse proxies, written in **PHP**.

**Guard** aims to make an easily configurable SSO handler, which works with various reverse proxies.
**Guard** will stay **simple**, without 2FA, LDAP support, etc. \
If you want these features, have a look at [Authelia](https://github.com/clems4ever/authelia) ;)

Currently supported reverse proxies:

- [Traefik](https://traefik.io/)
- _Coming soon_

## Getting started
Guard is not published on DockerHub yet but you can have a try using the example.

1. First, edit _/etc/hosts_ and add the following lines:
```
127.0.0.1       guard.local
127.0.0.1       auth.guard.local
```

2. Go to `app` and install PHP dependencies with _composer_ : `php composer.phar install` (or via `php composer install`)

3. Then, simply go to `examples` folder and run `docker-compose up --build` \
NB: `--build` is used to rebuild Guard image.

4. Open a browser and navigate to `http://guard.local`: you should be redirected to `https://auth.guard.local`.

5. Use the default credentials `user`/`password` so that you are redirected to the app. You're now logged in.