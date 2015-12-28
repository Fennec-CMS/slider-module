# Fennec CMS banner slider module

Usage:

### Default:

Instantiate Slider `Index\Controller` and echo the `loadSlider` method:

```
<?php
$slider = new \Fennec\Modules\Slider\Index();
echo $slider->loadSlider();
```

### Via AJAX:

Simply load the `/slider/` view:

jQuery example:

```
$.get('http://YOUR_SERVER/slider/', function(content) {
  $('#YOUR_SLIDER_CONTAINER').html(content);
});
```

