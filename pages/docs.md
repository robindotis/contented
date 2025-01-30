---
title: Documentation
date: 2025-01-25 12:00:00
---

## Why Contented🕸?

> Let a hundred blogging tools bloom; let a hundred schools of thought blog.
>
> -- With apologies to <cite>[Mao Zedong](https://en.wikipedia.org/wiki/Hundred_Flowers_Campaign)</cite>

The idea behind ***Contented*** 🕸 is to encourage building, hosting and managing independent blogs over the use one of the common blogging services. In the spirit of the [IndieWeb](https://indieweb.org/).

Why yet another blogging tool? The more independent tools there are, the more means of expression we have and the more ways of thinking are encouraged. 

***Contented*** 🕸 is not a service. It is a straightforward tool for creating a website. It is not a one click install tool, yet no coding knowledge is needed to put content online using ***Contented***🕸. The instructions make heavy use of GitHub, in order to get started. They are aimed at those with no prior experience of creating websites or using GitHub. A new website can be online within a couple of minutes by following the instructions. 

It helps if you are willing to play around a bit and explore the tool. If this is you, [keep reading](#table-of-contents), if not, these alternative independent online blogging services are highly recommended:

* [Bear Blog](https://bearblog.dev/)
* [PIKA](https://pika.page/)
* [micro.blog](https://micro.blog/)

## Table of Contents

1. [Installation Guide](#installation-guide)
    1. [Getting started](#getting-started)
1. [User Guide](#user-guide)
    1. [Site structure](#site-tructure)
        1. [Folders](#folders)
        1. [Files](#files)
    1. [File processing](#file-processing)
    1. [File structure](#file-structure)
        1. [Front Matter](#front-matter)
        1. [Content](#content)
    1. [Menus](#menus)
    1. [Metadata](#metadata)
1. [FAQ](#faq)
1. [References](#references)

## Installation Guide

### Getting started

To use ***Contented*** a [GitHub](https://github.com/) account is needed. Everything is done in GitHub: writing content, generating the site and publishing the pages. No other tool is needed other than a browser.

1. First of all sign up for a [GitHub account](https://github.com/signup) and then login to GitHub.
2. Next make a fork of ***Contented*** 🕸 by forking the ***Contented***🕸 repository. Name the fork username.github.io
3. Allow the actions to run.
4. Run the "Build site" action.
5. Settings -> Pages -> Deploy from Branch -> gh-pages -> Save
6. Wait one minute, reload the page, follow the link
7. Blog is live.

(see contentedweb as an example)


Notes:
* Start with simple setup of public rep names username.github.io and using branches for the deployment.
* have advanced instructions on how to setup to run with private repos and external hosting. 


Now explore GitHub a little. The [GitHub docs](https://docs.github.com/en/get-started) are very comprehensive. Being comfortable writing content and editing configuration files will be helpful, but this can also be learned. The [GitHub Writing guide](https://docs.github.com/en/get-started/writing-on-github) is maybe a good place to start for those new to GitHub.

### GitHub Pages

Pay attention to remove CNAME if the site is not loading properly. It should be removed if running under username.github.io. It should only be add if running under a custom domain name.

## User Guide

### Site tructure

#### Folders

| Folder | Description |
| --- | --- |
| assets | Contains static files, such as images, styling ([CSS](https://developer.mozilla.org/en-US/docs/Web/CSS)) and scripting ([JavaScript](https://developer.mozilla.org/en-US/docs/Web/JavaScript)). Folder will be copied as is to your website. |
| data | Contains configuration files, in [JSON](https://en.wikipedia.org/wiki/JSON) format. |
| feed | Contains code to generate the [RSS feed](https://en.wikipedia.org/wiki/RSS). |
| pages | Contains the site pages. These are standalone pages that will not be processed as posts and therefore won't appear in your feed. |
| posts | Contains the blog posts in [GitHub's Markdown](https://docs.github.com/en/get-started/writing-on-github/getting-started-with-writing-and-formatting-on-github) format. Subfolders are allowed to help find the posts (eg by year), but it won't affect how the posts are processed in GitHub's Markdown format. |
| themes | Contains template for the site design, using the [Twig](https://docs.github.com/en/get-started/writing-on-github) templating system. |
| uploads | Folder into which to upload images to process them into the [WEBP](https://en.wikipedia.org/wiki/WebP) image format. |

Only touch the **.github** folder if you know what you are doing. This contains the code that generates and publishes the website. 

#### Files

| Page | Description |
| --- | --- |
| robots.txt | Defines how search engines index the site. See [Google for further information on robots.txt](https://developers.google.com/search/docs/crawling-indexing/robots/intro). Modify only if needed. |
| _CNAME | Contains the custom domain if there is one. Rename it to "CNAME" if using a custom domain name with GitHub Pages. Otherwise ignore. |
| pages/home.md | Content for the homepage. The homepage markdown file should have the permalink front matter attribute defined as: `permalink: /`. One page, and only one page, should have this set otherwise the site will not have a homepage and visiting the base URL will return a page not found. |
| pages/404.md | Content to display when a page is not found. The page not found markdown file should have the permalink front matter attribute defined as: `permalink: /404.html`.  |
| feed/index.md |  Generates the RSS feed in Atom format. |
| pages/sitemap.md |  Generate a [sitemap.xml file](https://www.sitemaps.org/protocol.html) used by [search engines to index the site](https://developers.google.com/search/docs/crawling-indexing/sitemaps/build-sitemap). Remove if a sitemap is not needed (not recommended). |

Only touch the **index.php** file if you know what you are doing. This contains the code that processes the markdown files to generate the static HTML files.

### File processing
All content is stored in Markdown file, such as home.md.  Only files with the extension .md are processed onverted to HTML files. All other file types are ignored unless they are files in the assets folder, which are then simply copied.

Each markdown file, those with extension .md, is converted to an index.html file. This file is placed in a folder with the same path and name as the markdown file, unless specified otherwise in the Front Matter configuration for the file using the "permalink" attribute.

For example the file:

```
/posts/2023/summmer.md
```

would generated a file:

```
/posts/2023/summmer/index.html
```

which would be accessible using the URL:

```
/posts/2023/summmer/
```

And a file:

```
/pages/home.md
```

With the following front matter attribute:

```
permalink: /
```

Would generate file a here:

```
/index.html
```

Which would be accessible using this URL:

```
/
```

TO DO: explain slug "permalink": "/{{ filename }}/" as in pages.json.

### File structure
All Markdown files, whether posts or pages have two main parts. 

Firstly, a Front Matter part, which contains the configuration details for the file and is always at the beginning of the file. 

Secondly, the rest of the file which contains the content. 

#### Front Matter

The Front Matter section of the file has a specific structure which **must** be followed.

Firstly it always starts and ends with the line: `---`. 

Secondly it contains attribute / value pairs, one per line, eg: `title: Welcome to my blog`. The order of the attributes within the Front Matter is not important.

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

***Contented*** understands the following Front Matter attributes. Any other attributes will be ignored.

| Attribute | Value |
| --- | --- |
| title | Free text |
| date | ISO format YYYY-MM-DD hh:mm:ss eg "2025-01-01 08:00:00" |
| permalink | Any valid URL path, eg "/about/" |
| tags | Free text list of tags, one per line |
| prefix | Free text to add before the page name in the title bar |
| others? | TBD |

Note: you do not need to enclose the value in quotes in the Front Matter section.

### Content

In ***Contented***. all content is written using [GitHub's implementation of Markdown](https://docs.github.com/en/get-started/writing-on-github/getting-started-with-writing-and-formatting-on-github/basic-writing-and-formatting-syntax).

Note that Markdown can also contain HTML. So any tags for which there is no Markdown equivalent can be written in HTML.  

A typical Markdown content file might therefore look something like this:

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

### Archives

Archives are lists of all the posts matching a specific tag. 

They use the following Front Matter.

```
---
title: Coding 
pagination:
  data: web
  alias: posts

---
```

data is tag name to include in the archive.

### Menus

Menus can be defined for the header and footer. This is done in the file "_data/menus.yaml". The syntax of this file is a little more complicated as it is written in YAML.

[Learn more about the YAML syntax here](https://www.yaml.info/learn/index.html).

As an example this file menus.yaml file for a site with two menus. A header and a footer menu:

```
- name: header
  items:
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
      link: /tagging/angie/
      pre: 'the '
      post: '. '
- name: footer
  items:
    - title: RSS
      link: /feed/
      class: ally-statement
    - title: Accessibility
      link: /accessibility/
    - title: Site info
      link: /my-site/
```

### Metadata
Metadata for the site can be defined and is stored in the file "_data/metadata.yaml". 

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

## FAQ

How is being tied to GitHub in the spirit of the IndieWeb?
For those wanting to be independent of GitHub, nothing prevents this if you are happy to have a slightly more manual workflow. Or to Although.It is possible to run the script




## References

Below are some excellent links to further technical information and documentation on the technologies used for this site generator.

* Writing the content with [Markdown](https://docs.github.com/en/get-started/writing-on-github/getting-started-with-writing-and-formatting-on-github/basic-writing-and-formatting-syntax) and [HTML](https://developer.mozilla.org/en-US/docs/Web/HTML).
* Styling the site with ([CSS](https://developer.mozilla.org/en-US/docs/Web/CSS)).
* Scripting functionality with ([JavaScript](https://developer.mozilla.org/en-US/docs/Web/JavaScript)). 
* Modifying the sites structure using the [Twig template language](https://twig.symfony.com/).
* For the really adventurous: adapting the site generation engine using [PHP](https://www.w3schools.com/php/).
* Webhooks eg for EU hosting
* GitHub Pages
* RSS, sitemap, SEO etc
* https://stefanbohacek.com/blog/resources-for-keeping-the-web-free-open-and-poetic/ 

