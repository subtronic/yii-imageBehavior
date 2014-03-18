Image Behavior
=================
This behavior can be used for **single** image in model.
> This behavior save image in beforeSave event. 

Usage
======
Avaliable variables:
* typePrefix *(array)* -  contains file prefix for tmb and origin images;
* maxDimensions *(array)* - contains windth and height thumbnailed image in pixles;
* partPathFromBase *(string)* - path to dir from base, where will be saved images;
* partUrlFromHome *(string)* - URL to dir from roog, where images has saved;
* propertyName *(string)* - name of model attribute;
* uniqeAttrName *(string)* - name of attribute, which contains unique id model entity;

Example
=======
In model:
```php
...
public function behaviors()
{
    return array(
        'ImageBehavior' => array(
            'class' => 'application.components.behaviors.ImageBehavior',
        )
    );
}
...

public $typePrefix = array(
    'tmb' => 'tmb_',
    'origin' => 'origin_',
);
public $maxDimensions = array(
    'width' => 50,
    'height' => 50,
);
public $partPathFromBase = '/../upload/avatar/';
public $partUrlFromHome = 'upload/avatar/';
public $propertyName = 'avatar';
public $uniqeAttrName = 'id';
```
In View:
```php
echo CHtml::activeFileField($modelVariable, 'avatar');
```
In Controller:
```php
$model->attributes = $_POST['yourModelName'];
$model->save();
```
