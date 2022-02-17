2.0.4
=====

*   (improvement) Fix deprecations.


2.0.3
=====

*   (improvement) Allow Symfony v6.


2.0.2
=====

*   (improvement) Remove CacheClearerInterface in AssetsManager 
*   (improvement) Check if file exists in storeProcessableAsset AssetsStorage


2.0.1
=====

*   (bug) Support registering paths on Windows.
*   (improvement) Only support PHP 8.0.


2.0.0
=====

*   (bc) Adapt to new `_dependencies.json` storage directory + new layout.
*   (bc) CSS files are not hashed anymore, as they are already hashed in Firefly.
*   (bug) Fixed an invalid log message when dumping assets.


1.0.1
=====

*   (improvement) Integrate cache clearer in `AssetManager`.


1.0.0
=====

*   (improvement) Improve handling of large files. 
*   (improvement) Streamline processable file types.
*   (feature) Add loading and parsing of dependency maps.


0.1.0
=====

*   (feature) Initial release.
