# filters

The filter for XOOPS other than XCL (XOOPSCube Legacy)

## config

config filter has three points on getJS()

### PreBuild[Name].filter.php

On the point here, "$params" given before interpreting "$params" can be changed.

```php
class ckeditor4FilterConfigPreBuildConfig[NAME]
{
	public function filter(& $params) {

	}
}
```

### PreParseBuild[Name].filter.php

On the point here, a rated value can be given, before assembling "ckeditor.config"

```php
class ckeditor4FilterConfigPreParseBuild[NAME]
{
	public function filter(& $config, $params) {

	}
}
```

### PostBuild[NAME].filter.php

On the point here, assembled "ckeditor.config" can be overwritten.

```php
class ckeditor4FilterConfigPostBuild[NAME]
{
	public function filter(& $config, $params) {

	}
}
```
