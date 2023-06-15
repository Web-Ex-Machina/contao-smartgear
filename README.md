SMARTGEAR Project for Contao Open Source CMS
======================

The purpose of this project is to allow agencies, developers and webmasters to use Contao in a simpler way.

Functionnalities
-------------------
 * Merge several useful plugins
 * Adjust translations to make them more understandable
 * Upgrade backend UX
 * Add a lot of components, based on RSCE plugin

System requirements
-------------------

 * Contao 4.13
 * [Framway][4] (Now : this bundles contains a small prebuilt [Framway][4]. Future : will be automatically retrieved during first configuration)

System requirements (future)
-------------------

 * Access to `git` (to retrieve [Framway][4])
 * Access to `npm` (to build [Framway][4] - temp fix : using a `$nvmpath` environment variable to make `npm` available on our host)

Installation
------------

Clone the extension from Packagist (Contao Manager)

Testing with docker
-------------------

A `docker` folder is present in the project, it contains everything needed to run a new contao installation with this module installed on-the-go (linux only).

In your terminal, just type :

```
./docker/build.sh
```

The script will give you all options to run a working contao in a docker environment.

All the informations regarding path, ports and everything else are located in the [docker/.env file][5]

Documentation
-------------

 * [Change log][1]
 * [Git repository][2]

License
-------

This extension is licensed under the terms of the Apache License 2.0. The full license text is
available in the main folder.


Getting support
---------------

Visit the [support page][3] to submit an issue or just get in touch :)


Installing from Git
-------------------

You can get the extension with this repository URL : [Github][2]

[1]: CHANGELOG.md
[2]: https://github.com/webexmachina/contao-smartgear
[3]: https://www.webexmachina.fr/
[4]: https://framway.webexmachina.fr/
[5]: docker/.env