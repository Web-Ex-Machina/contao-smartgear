STYLE MANAGER
======================

The purpose of this document is to expose how we use the [Style Manager bundle][1].

Quick overview
--------------

The Style Manager bundles allow us to override the default `cssId` field in backend forms for contents, layouts, etc.. (see the bundle doc for a complete list) with `<select>` elements to choose CSS classes from.

We can create "archives" (ie. groups) to organize our CSS classes.

We can set the classes to be stored inside the `cssClasses` column of the elements, or to let the template access them and handle them itself.


Rules
-----

The archives we use will always have an `identifier` value starting by `fw`.

The styles we use will always have an `alias` value starting by `fw`.

By default, all the classes we create are automatically stores inside the `cssClasses` column of the elements it is applied to.

In case we don't want the classes to be automatically stored but rather accessed manually from the template, the archives' `identifier` and styles `alias` would be suffixed by the `_manual` value.

Special cases
--------------

In case we want to apply some classes on an element and some others on another element **inside the same template** (like for the [`rsce_gridGallery`][3]), we have to mix styles.

For the main element, we'll use classes that will automatically store themselves inside the `cssClasses`.

For the other elements, we'll use classes with the `passToTemplate` column set to `1` (done with the "*Use as template variable*" checkbox in the class definition form).

Inside the template, we'll access the "manual" classes thanks to the `$this->StyleManager` object.

[Full documentation on how to access class from inside the template][2].

[1]: https://github.com/oveleon/contao-component-style-manager/
[2]: https://github.com/oveleon/contao-component-style-manager/blob/master/docs/TEMPLATE_VARIABLES.md
[3]: ../src/Resources/public/contao_files/templates/rsce/rsce_gridGallery.html5