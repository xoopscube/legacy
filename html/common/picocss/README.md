# Pico.css 1.5.57 for XOOPSCube XCL 2.3.x

### Minimal CSS Framework for semantic HTML  

Elegant styles for all native HTML elements without <code>.classes</code> and dark mode automatically enabled.  
  
## Pico.css

**Class-light and semantic**  
Pico uses simple native HTML tags as much as possible. Less than 10 .classes are used in Pico.

**Great styles with just one CSS file**  
No dependencies, package manager, external files, or JavaScript.

**Responsive everything**  
Elegant and consistent adaptive spacings and typography on all devices.

**Light or Dark mode**  
Shipped with two beautiful color themes, automatically enabled according to the user preference.

## Usage

There are 4 ways to get started with pico.css:

**Install manually**

Edit your theme and link `/common/picocss/pico.min.css` in the `<head>` of your website.

```html
<link rel="stylesheet" href="<{$xoops_url}>/common/picocss/pico.min.css">
```

**Install from CDN**

Alternatively, you can use [unpkg CDN](https://unpkg.com/@picocss/pico@1.*/) to link pico.css.  
However, we recommend a "local-first" strategy to host your resources.  
Mainly due to the new features of modern browsers, Content Security Policy (CSP) and 
Subresource Integrity (SRI) which forces an update of the hash value every time you make a change.

```html
<link rel="stylesheet" href="https://unpkg.com/@picocss/pico@1.*/css/pico.min.css">
```

## Class-less version

Pico provides a `.classless` version ([example](https://picocss.com/examples/classless)).

In this version, `header`, `main` and `footer` act as containers.

Use the default `.classless` version if you need centered viewports:

#### Local

```html
<link rel="stylesheet" href="<{$xoops_url}>/common/picocss/pico.classless.min.css">
```

#### CDN

```html
<link rel="stylesheet" href="https://unpkg.com/@picocss/pico@1.*/css/pico.classless.min.css">
```

Or use the `.fluid.classless` version if you need a fluid container:

#### Local

```html
<link rel="stylesheet" href="<{$xoops_url}>/common/picocss/pico.fluid.classless.min.css">
```

#### CDN 

```html
<link rel="stylesheet" href="https://unpkg.com/@picocss/pico@1.*/css/pico.fluid.classless.min.css">
```

Then just write pure HTML, and it should look great:

```html
<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="<{$xoops_url}>/common/picocss/pico.classless.min.css">
    <title>Hello, world!</title>
  </head>
  <body>
    <main>
      <h1>Hello, world!</h1>
    </main>
  </body>
</html>
```

## Examples and Documentation

Minimalist templates to discover Pico in action:  

https://github.com/picocss/pico

## Copyright and license

Licensed under the [MIT License](https://github.com/picocss/pico/blob/master/LICENSE.md).
