# Drafts Plugin for Zuha & Cakephp #

Version 1

Mainly a behavior that adds the ability to easily save draft versions of a record without editing the current version of a record.  Used for things like editing blog posts or web pages, but saving changes which aren't ready for prime time just yet. 

## Installation ##

1. Create a plugins folder called Drafts
2. Put this plugin into that folder. (or git clone git@github.com:zuha/Drafts-Zuha-Cakephp-Plugin.git app/Plugin/Drafts)

## Save Usage ##

1. In the model you want to use drafts with, attach the Drafts.Draftable Behavior
2. The default settings should do most of the work, but if you need something custom change the settings during behavior attachment.
3. When you save a record add a field called 'draft' and set it to 1.  For example...
```php
		echo $this->Form->input('Article.draft', array('value' => 1))
```
4. Click save and instead of editing the article it will save a draft which you can then preview by going to the view and 

## View Usage ##
1. In your controller add a beforeFilter method, similar to this...
```php
		public function beforeFilter() {
			parent::beforeFilter();
			if (!empty($this->request->params['named']['draft'])) { 
				$this->Article->Behaviors->attach('Drafts.Draftable', array('returnVersion' => $this->request->params['named']['draft']));
				}
			}
		}
```
2. Visit a url similar to http://example.com/articles/articles/view/3/draft:1
3. It will return the results of the newest draft instead of the actual live version
4. Visit a url similar to http://example/articles/articles/view/3/draft:4
5. It will return the results of the fourth oldest draft instead of the actual live version
6. If there are only 2 drafts, then it will return the oldest available instead of the fourth. 

## Configuration Options ##

1. triggerField  ... Change the name of the field you send in a form from 'draft', to whatever you want.
2. foreignKeyName ... If you want to hard code something other than 'id' as the primary key.
3. reviseDateField ... unused as of 4/15/2012, meant to be for rolling back to older versions via post data.
4. conditions ... Set conditions so that viewing a draft only works with a filtered subset of records. (ie. Article.type = public)
5. returnVersion ... a setting to choose which version is returned during the find

## Callbacks ##

1. None

## Requirements ##

* PHP version: PHP 5.3+
* CakePHP version: 2.x Stable

## Support ##

For support and feature request, please visit submit an issue through Github.com.

For more information about our Professional Web Design, Development, and Marketing Services please visit the [Zuha Development Corporation website](http://razorit.com).

## License ##

Copyright 2009-2012, [RazorIT LLC](http://razorit.com)

Licensed under [GPLv3](http://www.gnu.org/licenses/gpl.html)
Redistributions of files must retain the below copyright notice.

## Copyright ###

Copyright 2009-2012
[RazoIT LLC](http://razorit.com)
8417 Oswego Rd. #121
Baldwinsville, NY 13027
http://razorit.com
