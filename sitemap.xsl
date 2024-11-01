<?xml version="1.0" encoding="UTF-8"?>
<xsl:stylesheet version="1.0"
    xmlns:html="http://www.w3.org/TR/REC-html40"
    xmlns:sitemap="http://www.sitemaps.org/schemas/sitemap/0.9"
    xmlns:xsl="http://www.w3.org/1999/XSL/Transform">
    <xsl:output method="html" version="1.0" encoding="UTF-8" indent="yes" />
    <xsl:template match="/">
        <html xmlns="http://www.w3.org/1999/xhtml">
            <head>
                <title>XML Sitemap</title>
                <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
                <style type="text/css">					
					#xml-head {
						background-color:#FAEBD7;
						border:1px #FAEBD7 solid;
						padding:10px 10px 10px 10px;
						margin:10px;
					}
					a {
						color:black;
					}
                </style>
            </head>
            <body>
                <xsl:apply-templates></xsl:apply-templates>
            </body>
        </html>
    </xsl:template>

    <xsl:template match="sitemap:urlset">
        <h1>XML Sitemap</h1>
        <div id="xml-head">
            <p>
                This XML sitemap provides search engines and users with links and structural information about this website, adhering to the widely accepted <a style='color:black;' href='https://sitemaps.org/'>XML sitemap standard</a>
            </p>
            <p>
                This file was dynamically generated using the WordPress CMS and <a style='color:black;' href='https://profiles.wordpress.org/weblineindia/#content-plugins'>XML Sitemap For Google</a> plugin developed by <a style='color:black;' href='https://www.weblineindia.com/wordpress-development.html'>WordPress plugin creation</a> team at WeblineIndia.
            </p>
        </div>
        <div id="content">
            <table cellpadding="5">
                <tr style="border-bottom:1px black solid;">
                    <th>Sr. No.</th>
                    <th>URL</th>
                    <th>Priority</th>
                    <th>Change frequency</th>
                    <th>Last modified Time (GMT)</th>
                </tr>
                <xsl:variable name="lower" select="'abcdefghijklmnopqrstuvwxyz'"/>
                <xsl:variable name="upper" select="'ABCDEFGHIJKLMNOPQRSTUVWXYZ'"/>
                <xsl:for-each select="./sitemap:url">
                    <tr>
                        <td>
                            <xsl:number value="position()" format="1"/>
                        </td>
                        <td>
                            <xsl:variable name="itemURL">
                                <xsl:value-of select="sitemap:loc"/>
                            </xsl:variable>
                            <a href="{$itemURL}">
                                <xsl:value-of select="sitemap:loc"/>
                            </a>
                        </td>
                        <td>
                            <xsl:value-of select="concat(sitemap:priority*100,'%')"/>
                        </td>
                        <td>
                            <xsl:value-of select="concat(translate(substring(sitemap:changefreq, 1, 1),concat($lower, $upper),concat($upper, $lower)),substring(sitemap:changefreq, 2))"/>
                        </td>
                        <td>
                            <xsl:value-of select="sitemap:lastmod"/>
                        </td>
                    </tr>
                </xsl:for-each>
            </table>
        </div>
    </xsl:template>

    <xsl:template match="sitemap:sitemapindex">
        <h1>XML Sitemap</h1>
        <div id="xml-head">
            <p>
                This XML sitemap provides search engines and users with links and structural information about this website, adhering to the widely accepted <a style='color:black;' href='https://sitemaps.org/'>XML sitemap standard</a>
            </p>
            <p>
                This file was dynamically generated using the WordPress CMS and <a style='color:black;' href='https://profiles.wordpress.org/weblineindia/#content-plugins'>XML Sitemap For Google</a> plugin developed by <a style='color:black;' href='https://www.weblineindia.com/wordpress-development.html'>WordPress plugin creation</a> team at WeblineIndia.
            </p>
        </div>
        <div id="content">
            <table cellpadding="5">
                <tr style="border-bottom:1px black solid;">
                    <th>Sr. No.</th>
                    <th>URL</th>
                    <th>Last Modified Time (GMT)</th>
                </tr>
                <xsl:for-each select="./sitemap:sitemap">
                    <tr>
                        <td>
                            <xsl:number value="position()" format="1"/>
                        </td>
                        <td>
                            <xsl:variable name="itemURL">
                                <xsl:value-of select="sitemap:loc"/>
                            </xsl:variable>
                            <a href="{$itemURL}">
                                <xsl:value-of select="sitemap:loc"/>
                            </a>
                        </td>
                        <td>
                            <xsl:value-of select="sitemap:lastmod"/>
                        </td>
                    </tr>
                </xsl:for-each>
            </table>
        </div>
    </xsl:template>

</xsl:stylesheet>