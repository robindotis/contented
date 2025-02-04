---
title: Documentation
date: 2025-01-25 12:00:00
---

## About Contented🕸

***Contented*** 🕸 is a simple script creating a static website. 

Content is created in text files, processed by the script and published on the web. That's it. 

A new website can be online within a couple of minutes. No coding knowledge required. 

If this sounds interesting to you and you would like your own independent part of the web, keep reading. All in the spirit of the [IndieWeb](https://indieweb.org/). 

## Table of Contents

1. [Getting started](#getting-started)
2. [Adding content](#adding-content)
3. [Folder structure](#folder-structure)
4. [Menus](#menus)
5. [Themes](#themes)
6. [Metadata](#metadata)
7. [Settings](#settings)
8. [FAQ](#faq)
9. [References](#references)

## Getting started

To get started with ***Contented*** 🕸, using these instructions, a [GitHub](https://github.com/) account is needed. 

Everything is done in GitHub: writing content, generating the site and publishing the pages. No other tool is needed other than your browser.

### GitHub account

If you don't already have one, you can sign up for a [GitHub account here](https://github.com/signup).

When picking your username, be aware that unless you have your own domain name, your website will use the domain name: YOURUSERNAME.github.io.

### Copy the script

Once your account is setup and you are logged in, you need to copy the script to your GitHub account. This is called forking a repository on GitHub. The steps below will create a repository with the script installed in it.

1. Visit this page: https://github.com/robindotis/robindotis.github.io
2. Click on the "Fork" button to the top right of the page.
3. Name the repository "YOURUSERNAME.github.io" and untick "Copy the main branch only".
4. Click on "Create Fork". 

### Build the site

Now that the repository is setup with the script, you need to build the site. This is done with [GitHub Actions](https://github.com/features/actions).

1. Click on "Actions" in the horizontal menu.
2. Accept the warning about workflows, by clicking on the green button.
3. Click on "Build site" in the left hand menu.
4. On the right hand side, click on the "Run workflow" dropdown, followed by the green "Run workflow" button. 
5. After a about a minute you should see a successful build, with a green tick next to the build name. 

Each time you make a change to your site you need to follow the above steps to rebuild the site. 

### Publish the site

Finally it's time to publish the site. For this we need to configuire [GitHub Pages](https://pages.github.com/).

1. Go to the GitHub Pages settings by clicking on "Settings" in the horizontal menu
2. Then click on "Pages" in the left hand menu.
3. Click on the "None" dropdown and choose "gh-pages".
4. Click on the "Save" button to the right "None" dropdown.
5. Wait one minute, reload the page and follow the link towards the top of the page.
6. Your site is live on YOURUSERNAME.github.io.

This is a one off step. Once GitHub Pages is configured changes to the site will be automatically published once they are built.

### Start Writing

Now explore GitHub a little. The [GitHub docs](https://docs.github.com/en/get-started) are very comprehensive. Being comfortable writing content and editing configuration files will be helpful, but this can also be learned. The [GitHub Writing guide](https://docs.github.com/en/get-started/writing-on-github) is maybe a good place to start for those new to GitHub.

### Final note

To use this script to generate your site, it helps if you are willing to play around a bit and explore these tool. If this is you, [keep going](/docs/adding-content/), if not, these alternative independent online blogging services are highly recommended:

* [Bear Blog](https://bearblog.dev/)
* [PIKA](https://pika.page/)
* [micro.blog](https://micro.blog/)

## Adding content

Files on GitHub can be browsed just like on your desktop. Click on "Code" in top right and you will see a list of all the files.

Content is added into Markdown files, with the extension ".md". Markdown is a very simple language for writing content that will be converted to HTML when the page is published. For more details on Markdown, please see [GitHub's Markdown guide](https://docs.github.com/en/get-started/writing-on-github/getting-started-with-writing-and-formatting-on-github/basic-writing-and-formatting-syntax).

Markdown can also contain HTML, so you can markup your content in plain HTML if you prefer. Mozilla provide a [very good HTML reference](https://developer.mozilla.org/en-US/docs/Web/HTML), if you are new to HTML.

Only files with the extension .md are processed and converted to HTML files. By default, all other file types are ignored unless they are files in the assets folder, which are simply copied to the live site.

### Front Matter

Each Markdown file can also contain settings specific to that page. This is called Front Matter and is always placed at the start of the file.

The Front Matter section of the file has a specific structure which **must** be followed.

Firstly it always starts and ends with the line: `---`. 

Secondly it contains attribute / value pairs, one per line, eg: `title: Welcome to my blog`. The order of the attributes within the Front Matter is not important.

Front Matter is written in YAML, which is also used for the site configuration (see later). Here is a good [guide to YAML](https://www.yaml.info/learn/index.html), although it might be a little technical for some.

If the attribute contains a list of values, it would be spread over multiple lines, eg:

```
tags:
- post
- travel
- photo 
```

The Front Matter of a typical post might therefore look something like this:

```
---
title: Happy New Year
date: 2025-01-01 08:00:00
tags:
- celebration
- new year
- photo
---
```

***Contented*** 🕸 understands the following Front Matter attributes. Any other attributes will be ignored.

| Attribute | Value |
| --- | --- |
| title | Free text. |
| date | ISO format YYYY-MM-DD hh:mm:ss eg "2025-01-01 08:00:00". Used for sorting posts. |
| permalink | Any valid URL path, eg "/about/". If not specified the link for the file will be the title. |
| tags | Free text list of tags, one per line. |
| prefix | Free text to add before the page name in the title bar. |
| navigation | For defining in which menu this page should appear. |
| - menu | Which menu the page appears in. Default templates support "header" and "footer". |
| - title | The text for the menu item. |
| - position | The position of the menu item within the menu. Should be a number. |
| - pre | Text to add before the link for the menu item. |
| - post | Text to add after the link for the menu item. |
| - class | CSS class to add to the menu item, for custom styling. |

Note: you do not need to enclose the value in quotes in the Front Matter section.

A page to be placed in the header menu would look like this:

```
---
title: About
date: 2025-02-01 08:00:00
navigation:
- menu: header
- title: About me
- position: 3
---
```

### Example markdown file

A typical complete Markdown file might therefore look something like this:

```
---
title: Happy New Year
date: 2025-01-01 08:00:00
tags:
- celebration
- new year
- photo
---

## Welcome

This is first blog post, I hope you enjoy this site.

<blockquote>Doing It Yourself Rocks!</blockquote>

```

## Folder structure

### Folders

These are the default folders. They should not be removed or renamed. More folders can be added without affecting the site.

| Folder | Description |
| --- | --- |
| assets | Contains static files, such as images, styling ([CSS](https://developer.mozilla.org/en-US/docs/Web/CSS)) and scripting ([JavaScript](https://developer.mozilla.org/en-US/docs/Web/JavaScript)). Folder will be copied as is to your website. |
| data | Contains configuration files, in [YAML](https://en.wikipedia.org/wiki/JSON) format. |
| feed | Contains code to generate the [RSS feed](https://en.wikipedia.org/wiki/RSS). |
| pages | Contains the site pages written in Markdown. These are standalone pages that will not be processed as posts and therefore won't appear in your feed. |
| posts | Contains the blog posts written in Markdown. Subfolders are allowed to help organise the posts (eg by year), but it won't affect how the posts are processed. |
| themes | Contains template for the site design, using the [Twig](https://docs.github.com/en/get-started/writing-on-github) templating system. |
| uploads | Folder into which to upload images to process them into the [WEBP](https://en.wikipedia.org/wiki/WebP) image format. |

Only touch the **.github** folder if you know what you are doing. This contains the code that generates and publishes the website. 

### Files

This is a list of specific files. You can edit them, but be sure you understand what you are doing. 

| Page | Description |
| --- | --- |
| robots.txt | Defines how search engines index the site. See [Google for further information on robots.txt](https://developers.google.com/search/docs/crawling-indexing/robots/intro). Modify only if needed. |
| _CNAME | Contains the custom domain if there is one. Rename it to "CNAME" if using a custom domain name with GitHub Pages. Otherwise ignore. |
| pages/home.md | Content for the homepage. The homepage markdown file should have the permalink front matter attribute defined as: `permalink: /`. One page, and only one page, should have this set otherwise the site will not have a homepage and visiting the base URL will return a page not found. |
| pages/404.md | Content to display when a page is not found. The page not found markdown file should have the permalink front matter attribute defined as: `permalink: /404.html`.  |
| feed/index.md |  Generates the RSS feed in Atom format. |
| pages/sitemap.md |  Generate a [sitemap.xml file](https://www.sitemaps.org/protocol.html) used by [search engines to index the site](https://developers.google.com/search/docs/crawling-indexing/sitemaps/build-sitemap). Remove if a sitemap is not needed (not recommended). |

Only touch the **index.php** file if you know what you are doing. This contains the code that processes the markdown files to generate the static HTML files.

## Menus

Menus can be defined for the header and footer. This is done in the file "/data/menus.yaml". The syntax of this file is a little more complicated as it is written in YAML.

[Learn more about the YAML syntax here](https://www.yaml.info/learn/index.html).

As an example this file menus.yaml file for a site with two menus, a header and a footer menu. You can add as many menus as you wish, but the default template only knows the header and footer menus. 

```
header:
    - title: Home
      link: /home/
    - title: About
      link: /about/
    - title: Blog
      link: /blog/
    - title: /walking
      link: /walking/
      pre: 'and '
    - title: dog 🐕‍🦺
      link: /tagging/dog/
      pre: 'the '
      post: '. '
- footer:
    - title: RSS
      link: /feed/
      class: ally-statement
    - title: Accessibility
      link: /accessibility/
    - title: Site info
      link: /my-site/
```

If menu items are defined in the menus.yaml file as well as in the Front Matter of specific pages, they are merged based on the position of the items. It is better to not define menu items in both the menus.yaml file and the page Front Matter as the results can be unexpected.

## Themes

Themes can be set in the settings.yaml file. 

Themes are created in the `/templates/` folder. They are build using the [Twig template language](https://twig.symfony.com/).

If not set it will take the default templates from the folder: `/templates/default/`. If that folder does not exist, the site cannot be built.

A theme can extend another theme, as defined by the `themeExtends` setting.

## Metadata

Metadata is information about the site. It is stored in YAML format in the file `/data/metadata.yaml`. This metadata can be accessed from the site's template, for example to display the title of the site.

This is an example the metadata.yaml file for a basic site:

```
title: Robin's
url: https://www.robin.is/
urlShort: robin.is
language: en
description: Setup and host your own independent blog
startYear: 2025
relMe: https://github.com/robindotis/
gitHubRepo: https://github.com/robindotis/robin.is/
theme: sky
feed:
  subtitle: Setup and host your own independent blog
  filename: index.xml
  path: /feed/index.xml
  id: https://www.robin.is/
author:
  name: robindotis
  email: admin@robin.is
  url: https://www.robin.is/about/
  webmention: https://github.com/robindotis/
```

## Settings

Settings are set in the file: `/data/setting.yaml`. These settings define how the content should be processed. Do not touch unless you are sure you know what you are doing.

Allowed settings are:

| Name | Default | Description |
|---|---|---|
| staticDirs | [assets] | List of folders containing static files to be copied as is to the output directory. |
| staticFiles | [robots.txt,feed/pretty-feed-v3.xsl] | List of static files to be copied as is to the output directory. |
| sourceDirs | [posts, pages, feed] | List of folders to be processed. Only Markdown files will be processed. |
| outputDir | /_site/ | The folder in to which the files for the static site will be placed. |
| theme | matrix | The name of the folder containing the templates for the site. |
| themeExtends | default | The name of the folder containing the theme that `theme` builds up on, if any. |

All settings are optional. If they are not provided, defaults will be used.

The default settings.yaml file looks like this:

```
staticDirs: [assets]
staticFiles: [robots.txt,feed/pretty-feed-v3.xsl]
sourceDirs: [posts, pages, feed]
outputDir: /_site/
```

## References

Below are some excellent links to further technical information and documentation on the technologies used for this site generator.

* Writing the content with [Markdown](https://docs.github.com/en/get-started/writing-on-github/getting-started-with-writing-and-formatting-on-github/basic-writing-and-formatting-syntax) and [HTML](https://developer.mozilla.org/en-US/docs/Web/HTML).
* Styling the site with ([CSS](https://developer.mozilla.org/en-US/docs/Web/CSS)).
* Scripting functionality with ([JavaScript](https://developer.mozilla.org/en-US/docs/Web/JavaScript)). 
* Modifying the sites structure using the [Twig template language](https://twig.symfony.com/).
* For the really adventurous: adapting the site generation engine using [PHP](https://www.w3schools.com/php/).
* [GitHub Actions](https://docs.github.com/en/actions)
* [GitHub Pages](https://docs.github.com/en/pages/getting-started-with-github-pages/about-github-pages)
* [Webhooks](https://docs.github.com/en/webhooks)
* [RSS](https://www.rssboard.org/rss-specification) and [Atom](https://en.wikipedia.org/wiki/Atom_(web_standard))
* [Sitemap.xml](https://developers.google.com/search/docs/crawling-indexing/sitemaps/overview)

