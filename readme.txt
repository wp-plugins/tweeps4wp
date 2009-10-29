=== Tweeps4WP ===
Contributors: anoopbhat, reaperhulk
Donate link: http://wp.anoop.net/tweeps4wp
Tags: twitter, following, friends, widgets
Requires at least: 2.8
Tested to: 2.8.5
Stable tag: 0.0.5

A simple widget that enumerates the list of users you're following or the users that are following you

== Description ==

[Tweeps4WP](http://wp.anoop.net/tweeps4wp/ "Tweeps4WP Home") is a WordPress widget that displays the latest list of followers or friends for your twitter account

Tweeps4WP requires PHP 5.0 or newer.

Tweeps4WP requires curl as well. it will not work without curl.

Tweeps4WP requires SimpleXML as well

For it to work, you need a twitter account and must store your password in the
database. Once stored, it is never revealed anywhere and only used when making
the calls to twitter's api.

The XML returned from twitter is stored in a database for a period of time.
Minimum 10 minutes. If expired, it will automatically refresh itself.
This is in place so that the twitter servers are not overwhelmed for any
reason. Also, because you can easily lock your account out or something if you
violate the quotas.

The photos link to the twitter pages of your tweep.

== Changelog ==

= 0.0.5 = 
bug in 0.0.4. Found immediately. normal images were no longer displaying correctly. Fixed it so mini resizes to 24x24 and normal to 48x48. 

= 0.0.4 =
mini images from twitter aren't always mini. They're called mini but sometimes they're normal. This update ensures that all images are resized to 24x24 just like twitter.com does.

= 0.0.3 =
* Having an incorrect password produces all sorts of nasty errors. Made these errors go away and simply display a message stating that the list is not available at this time. 

= 0.0.2 =
* fixed justification issues and tested in Firefox, IE7, Safari, and Opera

== Installation ==

1. Upload Tweeps4WP/ into wp-content/plugins/
2. Activate the plugin via the 'Plugins' menu.
3. Add/configure the widget via the 'Widgets' option under Themes.

== Frequently Asked Questions ==

= Why did you write this plugin? =
I couldn't find one anywhere else and it seemed like a good idea for a first
plugin/widget.

== Screenshots ==

1. The default list of followers/friends with a mini profile view
1. The list of followers/friends with a normal profile view
2. The configuration panel for the widget.

== License ==

    Copyright 2009 Anoop Bhat

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 2 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA

