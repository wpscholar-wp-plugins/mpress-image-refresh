=== Image Refresh ===
Contributors: wpscholar
Donate link: https://www.paypal.me/wpscholar
Tags: random image, image reload, image refresh, image rotation, different image, attachments, image with caption
Requires at least: 4.5
Requires PHP: 5.4
Tested up to: 6.7
Stable tag: 2.3.1
License: GPLv3
License URI: http://www.gnu.org/licenses/gpl-3.0.html

Show a fresh image on every page load.

== Description ==

The **Image Refresh** plugin allows you to randomly display a different image on every page load.

= Why? =

Many people like to vary up the images on their site just to keep things fresh.  Loading a new image every time someone visits your homepage is something that should be simple, right? Alas, people resort to using plugins that are not meant for this purpose and end up sacrificing performance for the sake of a fresh look. Well, my friend, the search has ended. This plugin does nothing more, and nothing less than load that fresh image where you need it.  To top it all off, we do it in a way that won't slow your site down.

= How? =

Using this plugin is simple:

1. Install the plugin
2. Activate the plugin
3. On the page or post where you want to have an image refresh, just click on the 'Add Media' button, upload as many images as you like, click 'Save Changes' and exit the media uploader.
4. Add the `[mpress_image_refresh]` shortcode where you want your image to appear.
5. Save your changes.

= Features =

* Works with custom post types
* No settings page, just an easy way for you to add fresh images to your site
* Clean, well written code that won't bog down your site

== Installation ==

= Prerequisites =
If you don't meet the below requirements, I highly recommend you upgrade your WordPress install or move to a web host
that supports a more recent version of PHP.

* Requires WordPress version 3.2 or greater
* Requires PHP version 5 or greater ( PHP version 5.2.4 is required to run WordPress version 3.2 )

= The Easy Way =

1. In your WordPress admin, go to 'Plugins' and then click on 'Add New'.
2. In the search box, type in 'mPress Image Refresh' and hit enter.  This plugin should be the first and likely the only result.
3. Click on the 'Install' link.
4. Once installed, click the 'Activate this plugin' link.

= The Hard Way =

1. Download the .zip file containing the plugin.
2. Upload the file into your `/wp-content/plugins/` directory and unzip
3. Find the plugin in the WordPress admin on the 'Plugins' page and click 'Activate'

= Usage Instructions =

Once the plugin is installed and activated, go to any page or post where you want to have a fresh image display on each page load.  Just above the content editing area, click on the 'Add Media' icon to bring up the media uploader. Upload one or more images and click 'Save Changes' before exiting out of the popup window. Add the `[mpress_image_refresh]` shortcode in the content area where you want your image to appear.  Save your changes and check out the results!

== Frequently Asked Questions ==

The `[mpress_image_refresh]` shortcode supports a few attributes to give you more control over the results:

* **attachment** - You can pass in a comma separated list of image IDs if you want to directly specify a set of images to use. Using this attribute invalidates the `post_id` and `not` attributes. Example: `[mpress_image_refresh attachment="19, 37, 940"]` Optionally, you can also have the shortcode randomly pull from a subset using this syntax: `[mpress_image_refresh attachment="19,37,940|453|98"]`

* **post_id** - You can pass in the post ID to pull attached images from.  If your images are attached to another post, just provide the ID of that post and we will pull those images instead.  Example: `[mpress_image_refresh post_id="19"]`

* **size** - By default, we use the 'large' image size defined by WordPress.  However, you can use the other built-in image sizes ( 'thumbnail', 'medium', 'full' ) or any custom image sizes you have defined. Example: `[mpress_image_refresh size="full"]`

* **class** - Set a custom class on your image using this attribute so you can apply custom styling. Example: `[mpress_image_refresh class="fresh-image"]`

* **not** - Sometimes you have an image within the page content, and as such it is automatically attached to the page or post.  You probably don't want these images to be used as one of your random images, so just pass in the ID of those images to keep those from showing up.  Example: `[mpress_image_refresh not="2310, 2319"]`

* **caption** - You can pass in 'true' or 'false'.  If set to 'true', then the caption will be displayed below the image.  Example: `[mpress_image_refresh attachment="5,7,9" caption="true"]`.

Keep in mind that any of these attributes can be combined as needed.  Example: `[mpress_image_refresh post_id="19" size="full" not="2310"]`.  Also, keep in mind that the shortcode can be used in text widgets.  However, you will probably want to define the **post_id** attribute when using the shortcode in a text widget.

= Actions and Filters =

* `mpress_image_refresh-attachment_id` -- Filter the attachment ID selected for display. Parsed shortcode attributes are provided as an additional parameter to the callback function.
* `mpress_image_refresh-image_atts` -- Filter the image attributes. The attachment post object and parsed shortcode attributes are provided as additional parameters to the callback function.
* `mpress_image_refresh-markup` -- Filter the markup surrounding the image. The attachment post object and parsed shortcode attributes are provided as additional parameters to the callback function.
* `mpress_image_refresh-image_html` -- Filter removed in version 2.0. Use `mpress_image_refresh-markup` filter instead.

== Changelog ==

= 2.3.1 = 

* General maintenance

= 2.3 =

* Update compatibility with WP 6.5
* Add ability to randomly select an attachment ID from a subset.

= 2.2.1 =

* Fix fatal error in WP 6.0.1

= 2.2 =

* Added ability to specify a link URL for images.

= 2.1.1 =

* Fixed bug where no image was shown on 404 pages when explicitly providing a collection of attachment IDs.

= 2.1 =

* Fix to allow a 'full' image size to be used.
* Added note to help messages letting the user know only logged in users with the ability to edit the shortcode can see the notification.

= 2.0 =

* Added helpful error messages for content editors when there is an issue with the shortcode implementation.
* Updated plugin to provide a number of new filters for developers.
* Updated plugin to make it translatable.
* Tested in WordPress version 4.7.2

= 1.0 =

* Tested in WordPress version 4.5.2

= 0.4 =

* Updated plugin to be more friendly with WPEngine's WordPress hosting  environment.

= 0.3 =

* Added ability to display image captions, if desired.
* Added filter `mpress_image_refresh-image_html` to allow for HTML customizations.
* Updated plugin 'Tested Up To' tag to WordPress version 4.1

= 0.2 =

* Updated plugin 'Tested Up To' tag to WordPress version 4.0
* Added 'attachment' shortcode attribute for users who want to specify a set of images directly from the Media Library.

= 0.1 =

* Initial commit

== Upgrade Notice ==

= 2.3 =

* Add new feature to shortcode to allow random subsets

= 2.2.1 =

* Bug fixes.

= 2.2 =

* Adds ability to specify a link URL for images.

= 2.1.1 =

* Fixed bug where no image was shown on 404 pages when explicitly providing a collection of attachment IDs.

= 2.1 =

* Fix to allow a 'full' image size to be used.
* Added note to help messages letting informing that only logged in users with the ability to edit the shortcode can see the notification.

= 2.0 =

* Added helpful error messages for content editors when there is an issue with the shortcode implementation.
* Updated plugin to provide a number of new filters for developers.
* Updated plugin to make it translatable.
* Tested in WordPress version 4.7.2

= 1.0 =

* Plugin updated to reflect that it works with WordPress version 4.5.2

= 0.4 =

* Updated plugin to be more friendly with WPEngine's WordPress hosting environment.

= 0.3 =

* You can now opt to display image captions, if available.

= 0.2 =

* You can now specify a set of image IDs using the 'attachment' shortcode attribute in order to load images directly from the Media Library.
