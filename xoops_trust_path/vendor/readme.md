!/vendor/autoload.php

# Autoloader optimization

By default, the Composer autoloader runs relatively fast.
However, due to the way PSR-4 and PSR-0 autoloading rules are set up,
it needs to check the filesystem before resolving a classname conclusively.
This slows things down quite a bit, but it is convenient in development
environments because when you add a new class it can immediately be discovered
and used without having to rebuild the autoloader configuration.

The problem however is in production you generally want things to happen as fast
as possible, as you can rebuild the configuration every time you deploy and new
classes do not appear at random between deploys.

For this reason, Composer offers a few strategies to optimize the autoloader.

**Note:** You should not enable any of these optimizations in development
as they all will cause various problems when adding/removing classes.
The performance gains are not worth the trouble in a development setting.

https://getcomposer.org/doc/articles/autoloader-optimization.md

