=== Sola Scripturae Catenata ===
Contributors: versorverbi
Tags: bible, catholic, church, Jesus, Christ, Pope, Vatican, scripture, deuterocanon, apocrypha, Christian
Requires at least: 3.0.1
Tested up to: 4.6.1
Stable tag: 4.3
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Sola Scripturae Catenata ("Only Links to Scripture") converts Bible references on your site into hyperlinks to BibleGateway.com.

== Description ==

Sola Scripturae Catenata ("Only Links to Scripture") converts Bible references in your posts and comments into hyperlinks to the online translation of your choice through BibleGateway.com.

For example, it will change John 3:16 into something like <a href="https://www.biblegateway.com/passage/?search=John+3:16&version=NRSV">John 3:16</a>

All English Bible translations at [BibleGateway.com](http://www.biblegateway.com/) (even those including the Deuterocanon and Apocrypha) are currently supported.

== Installation ==

1. Upload `sola-scripturae-catenata.php` to the `/wp-content/plugins/sola-scripturae-catenata/` directory, or install the plugin using WordPress' ZIP file install feature
1. Activate the plugin through the 'Plugins' screen in WordPress
1. Go to Settings > Sola Scripturae Catenata to select your preferred default translation

== Frequently Asked Questions ==

= How does SSC recognize Scripture references? =

1. SSC abides by a standard format:
 - [Book] [Chapter]
 - [Book] [Chapter]:[Verse]
2. [Book] can be the full name of the book or a standard abbreviation, and should include a prefix or volume number where applicable. Prefixes can appear in the following formats and still be recognized:
 - 1, 2, 3, 4
 - I, II, III, IV
 - First, Second, Third, Fourth
 - 1st, 2nd, 3rd, 4th
3. [Chapter] and [Verse] should be Arabic numerals (1,2,3,...).
4. If you want to include multiple chapters or verses from the same book, use semicolons (;) between individual chapters and commas (,) between individual verses (e.g., John 3; 7 for John chapters 3 and 7, or John 3:16, 19 for verses 16 and 19 of John chapter 3), or a hyphen (-) between two verse numbers for a range of verses (e.g., John 3:16-19).
5. The default translation is the NRSV. To use a different translation, go to Settings > Sola Scripturae Catenata and select one from the drop-down.

= Are you planning to add support for other languages? =

Not at this time, though I am willing to entertain offers to help. (The main obstacle is lack of time.)

== Screenshots ==

N/A

== Changelog ==

= 2.3 =
* Added a shortcode ([noref][/noref]) to put around a single reference which prevents that reference from being linked.

= 2.2.1 =
* References to a volumed book following a reference now work (i.e., in the past, "John 1; 2 John 1" referred to John 1, then John 2, then John 1 again; the links are now correctly to John 1 and II John 3.
* References to multiple passages of a translated book now work. E.g., if you referenced Romans 1-2, and had translated Romans 1 and 2, the link pointed to BibleGateway, not the local blog; now it points to the first chapter in the reference.

= 2.2 =
* References to Isaiah work again (no longer match on volume)
* References to multiple chapters of the same book are broken out into multiple links; this allows each to be examined individually, but also allows one to link to chapter that exists as an internal post (e.g., Romans 3) and another that doesn't (e.g., Romans 5). Therefore, when /romans-3/ exists and /romans-5/ does not, the first link for "Romans 3:23; 5:25" will be to the internal post, and the second will be to BibleGateway.com as expected.
* The code has been cleaned up significantly; it is now organized and slightly more readable

= 2.1.2 =
* References to the epistles I, II, and III John no longer link to the Gospel John

= 2.1.1 =
* "Is \w" no longer matches the algorithm unless it really is a reference to Isaiah

= 2.1 =
* Added a function to allow linking to a post in your own WP instance instead of an online Bible (for users who have their own translations of passages). The function assumes that the desired post is named [Book] [Chapter]:[Verse]. [Verse] is optional.
* Added a setting on the settings page to control whether self-linking should be attempted.

= 2.0 =
* Added a function to fix the numbering of the Psalms when a Septuagint-based translation (i.e., the Douay-Rheims) is used
* Added a Settings page to allow the user to determine which translation to use
* Added a function to change the translation if the default does not include the referenced text (e.g., a New Testament-only translation is the default, but an Old Testament book is referenced)
* Recoded, redocumented, and redeveloped plugin into SSC (Sola Scripturae Catenata).

= 1.5 =
* Fixed a bug (limit added to preg_replace_callback to prevent references to, e.g., I Corinthians 1:1 overriding II Corinthians 1:1)
* Changed links from bibliacatolica.com.br to BibleGateway.com 
* Allowed for references to IV/4th/Fourth/4 books (e.g., IV Maccabees)
* Allowed for references to multiple chapters/verses in the same book (e.g., Genesis 1:2; 3:4, 5)
* Allowed for multiple references to the same/similar verses (e.g., formerly, "Romans 16" overrode "Romans 16:1", if you had both in the same text)
* Expanded list of books to include Deuterocanon and Apocrypha appropriately (original plugin only allowed Portuguese abbreviations to refer to those books)

= 1.0 =
* Original release of source plugin (Catholic Bible Scripturizer).


== Upgrade Notice ==

= 2.3.0 =
Enhancement. Added a shortcode to place around a single reference to prevent linking for that reference.

= 2.2.1 =
Bug fix. Subsequent references to volumed books were breaking; they work now. References to multiple chapters of a translated passage did not link to the blog, but to the external Bible; provided the first chapter in the reference is on the local blog, these now link there.

= 2.2.0 =
Bug fix and small enhancement. References to Isaiah were breaking; they work again. References to multiple chapters at the same time are now multiple links instead of one.

= 2.1.2 =
Bug fix. References to the epistles of John were linking to the Gospel of John. They no longer do.

= 2.1.1 =
Bug fix. Some strings were matching the algorithm even though they weren't Scripture references.

= 2.1.0 =
Added capacity to link to internal posts for those users who write their own translations.

= 2.0.0 =
Significant improvement over the previous version. Any English translation on [BibleGateway](http://www.biblegateway.com/) is now available through a new Settings page. References to translations with Septuagint enumeration now will reach the right Psalm.

= 1.5 =
Fixes significant bugs that interfere with proper use. Multiple references on the same page are now possible. An English translation is now possible.