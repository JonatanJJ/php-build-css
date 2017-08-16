# php-build-css
Helper function for generating css with PHP.
Useful for example to avoiding repitition when nesting selectors, or when working with dynamic CSS.

When working with dynamic CSS it may often be easier to store and modify that CSS in an associative array.
This function allows for easily converting an associative array to CSS.

## Example usage

### Simple usage:
```php
<style>
<?php
	echo build_css(array(
		'background' => '#000',
		'color' => '#FFF',
	), 'div');
?>
</style>
```
Output:
```html
<style>
div{
	background: #000;
	color: #FFF;
}
</style>
```
---
### CSS attribute with multiple values:
```php
<style>
<?php
	echo build_css(array(
		'background-color' => array(
			'rgb(212,228,239)', // Fallback value
			'linear-gradient(to right, rgba(212,228,239,1) 0%, rgba(134,174,204,1) 100%)'
		),
	), 'div');
?>
</style>
```
Output:
```html
<style>
div{
	background-color: rgb(212,228,239);
	background-color: linear-gradient(to right, rgba(212,228,239,1) 0%, rgba(134,174,204,1) 100%);
}
</style>
```
---
### Using nested selectors, and CSS string:
```php
<style>
<?php
	echo build_css(array(
		'background' => '#000',
		'& button, & .btn' => array( // Nested selector
			'color' => '#333',
			'font-size' => '1.5em',
			'&:hover, &:active, &:focus' => array( // Nested selector
				'color' => '#666',
			),
		),
		'& input' => 'color: #6e6;', // Nested selector with CSS string
	), 'form');
?>
</style>
```
Output:
```html
<style>
form{
	background: #000;
}
form button, form .btn{
	color: #333;
	font-size: 1.5em;
}
form button:hover, form button:active, form button:focus,
form .btn:hover, form .btn:active, form .btn:focus{
	color: #666;
}
form input{
	color: #6e6;
}
</style>
```
---
### Using in style attribute:
```php
<div style="<?php echo build_css_attr(array(
  'background' => '#333',
  'color' => '#FFF',
)) ?>">
</div>
```
Output:
```html
<div style="background: #333;color: #FFF;">
</div>
```