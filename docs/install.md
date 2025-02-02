---
title: Getting Started
date: 2025-01-25 12:00:00
---

To get started with ***Contented*** 🕸, a [GitHub](https://github.com/) account is needed. 

Everything is done in GitHub: writing content, generating the site and publishing the pages. No other tool is needed other than your browser.

## GitHub account

If you don't already have one, you can sign up for a [GitHub account here](https://github.com/signup).

When picking your username, be aware that unless you have your own domain name, your website will use the domain name: YOURUSERNAME.github.io.

## Copying the code

Once your account is setup and you are logged in, you need to copy the code for script to your account. This is called forking a repository on GitHub.

1. Visit this page: https://github.com/robindotis/robindotis.github.io
2. Click on the "Fork" button to the top right of the page. ![](/assets/images/docs/fork.png)
3. Name the repository "YOURUSERNAME.github.io" and untick "Copy the main branch only". ![](/assets/images/docs/fork-create.png)
4. Click on "Create Fork". 

## Building your site

Next you have to create the site.

1. Click on "Actions" and accept the warning about workflows. ![](/assets/images/docs/actions-accept.png)
2. Click on "Build site". ![](/assets/images/docs/actions-build-site.png)
3. Then click on the "Run workflow" dropdown, followed by the "Run workflow" button. ![](/assets/images/docs/actions-run-workflow.png) 
4. After a about a minute you should see a successful build. ![](/assets/images/docs/actions-build-success.png) 

## Publish your site

Finally turn on GitHub Pages.

1. Go to the GitHub Pages settings by clicking on "Settings", then "Pages". ![](/assets/images/docs/pages-settings.png)
2. Click on the "None" dropdown and choose "gh-pages". ![](/assets/images/docs/pages-branch.png)
3. Finally click on the "Save". ![](/assets/images/docs/pages-save.png)
4. Wait one minute, reload the page, follow the link. ![](/assets/images/docs/pages-link.png)
5. Your site is live on YOURUSERNAME.github.io.

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

### Themes

Themes can be set in the settings.yaml file. 

Themes are created in the `/templates/` folder.

If not set it will take the default templates from the folder: `/templates/default/`. If that folder is not exist, the site will cannot be built.

A theme can extend another theme, as defined by the `themeExtends` setting.

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

**Consider renaming this to "configuration", config.yaml**

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

### Settings

Settings are set in the file: `/data/setting.yaml`.

Allowed settings are:

| Name | Default | Description |
|---|---|---|
| staticDirs | [assets] | List of folders containing static files to be copied as is to the output directory. |
| staticFiles | [robots.txt,feed/pretty-feed-v3.xsl] | List of static files to be copied as is to the output directory. |
| sourceDirs | [posts, pages, feed] | List of folders to be processed. Only Markdown files will be processed. |
| outputDir | /_site/ | The folder in to which the files for the static site will be placed. |
| theme | matrix | The name of the folder containing the templates for the site. |
| themeExtends | default | The name of the folder containing the theme that `theme` builds up on, if any. |

The default settings.yaml file looks like this:

```
staticDirs: [assets]
staticFiles: [robots.txt,feed/pretty-feed-v3.xsl]
sourceDirs: [posts, pages, feed]
outputDir: /_site/
theme: matrix
themeExtends: default
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

