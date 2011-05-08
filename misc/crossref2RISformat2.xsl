<?xml version="1.0"?>
<xsl:stylesheet xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
                xmlns="http://periapsis.org/tellico/"
                xmlns:cr="http://www.crossref.org/xschema/1.0"
                xmlns:str="http://exslt.org/strings"
                xmlns:exsl="http://exslt.org/common"
                exclude-result-prefixes="cr"
                extension-element-prefixes="str exsl"
                version="1.0">

<!--
   ===================================================================
   Tellico XSLT file - used for importing data from crossref.org
   in the 'unixref' format. So far, I haven't found a schema definition

   Copyright (C) 2008 Robby Stephenson - robby@periapsis.org

   This XSLT stylesheet is designed to be used with the 'Tellico'
   application, which can be found at http://www.periapsis.org/tellico/

   ===================================================================
-->

<xsl:output method="text" version="1.0" encoding="UTF-8" indent="yes"
            doctype-public="-//Robby Stephenson/DTD Tellico V10.0//EN"
            doctype-system="http://periapsis.org/tellico/dtd/v10/tellico.dtd"/>

<!-- by default, don't output text -->
<xsl:template match="text()" />

<xsl:template match="/">
   <fields>
    <field name="_default"/>
    <xsl:if test=".//cr:issn">
     <field flags="0" title="ISSN" category="Publishing" format="4" type="1" name="issn" i18n="true"/>
    </xsl:if>
   </fields>
   <xsl:apply-templates select="cr:doi_records/cr:doi_record/cr:crossref"/>
</xsl:template>

<xsl:template match="cr:crossref">
  <xsl:apply-templates/>
</xsl:template>

<xsl:template match="cr:book">
<xsl:text>TY  - BOOK&#xa;</xsl:text>
 <xsl:apply-templates/>
</xsl:template>

<xsl:template match="cr:journal">
<xsl:text>TY  - JOUR&#xa;</xsl:text>
 <xsl:apply-templates/>
</xsl:template>

<xsl:template match="cr:book_metadata">
<xsl:text>BT  - </xsl:text><xsl:value-of select="cr:titles/cr:title[1]"/><xsl:text>&#xa;</xsl:text>
 <xsl:apply-templates/>
</xsl:template>

<xsl:template match="cr:journal_article">
<xsl:text>TI  - </xsl:text><xsl:value-of select="cr:titles/cr:title[1]"/><xsl:text>&#xa;</xsl:text>
 <xsl:apply-templates/>
</xsl:template>

<xsl:template match="cr:isbn">
<xsl:text>SN  - </xsl:text><xsl:value-of select="."/><xsl:text>&#xa;</xsl:text>
<xsl:text>N1  - ISBN: </xsl:text><xsl:value-of select="."/><xsl:text>&#xa;</xsl:text>
</xsl:template>

<xsl:template match="cr:issn">
<xsl:text>SN  - </xsl:text><xsl:value-of select="."/><xsl:text>&#xa;</xsl:text>
<xsl:text>N1  - ISSN: </xsl:text><xsl:value-of select="."/><xsl:text>&#xa;</xsl:text>
</xsl:template>

<xsl:template match="cr:publisher">
<xsl:text>PB  - </xsl:text><xsl:value-of select="cr:publisher_name"/><xsl:text>&#xa;</xsl:text>
<xsl:text>CY  - </xsl:text><xsl:value-of select="cr:publisher_place"/><xsl:text>&#xa;</xsl:text>
</xsl:template>

<xsl:template match="cr:journal_metadata">
<xsl:text>JF  - </xsl:text><xsl:value-of select="cr:full_title"/><xsl:text>&#xa;</xsl:text>
 <xsl:apply-templates/>
</xsl:template>

<xsl:template match="cr:edition">
<xsl:text>ET  - </xsl:text><xsl:value-of select="."/><xsl:text>&#xa;</xsl:text>
<xsl:text>N1  - Edition:</xsl:text><xsl:value-of select="."/><xsl:text>&#xa;</xsl:text>
</xsl:template>

<xsl:template match="cr:volume">
<xsl:text>VL  - </xsl:text><xsl:value-of select="."/><xsl:text>&#xa;</xsl:text>
</xsl:template>

<xsl:template match="cr:issue">
<xsl:text>IS  - </xsl:text><xsl:value-of select="."/><xsl:text>&#xa;</xsl:text>
</xsl:template>

<xsl:template match="cr:series_metadata">
<xsl:text>T3  - </xsl:text><xsl:value-of select="cr:titles/cr:title[1]"/><xsl:text>&#xa;</xsl:text>
</xsl:template>

<xsl:template match="cr:doi_data">
<xsl:text>UR  - http://dx.doi.org/</xsl:text><xsl:value-of select="cr:doi"/><xsl:text>&#xa;</xsl:text>
<xsl:text>UR  - </xsl:text><xsl:value-of select="cr:resource"/><xsl:text>&#xa;</xsl:text>
</xsl:template>

<xsl:template match="cr:publication_date">
<xsl:text>PY  - </xsl:text><xsl:value-of select="cr:year"/><xsl:text>&#xa;</xsl:text>
<xsl:text>Y2  - </xsl:text><xsl:value-of select="cr:year"/><xsl:text> - </xsl:text><xsl:value-of select="cr:month"/><xsl:text>&#xa;</xsl:text>
 
</xsl:template>

<xsl:template match="cr:pages">
<xsl:text>SP  - </xsl:text><xsl:value-of select="cr:first_page"/><xsl:text>&#xa;</xsl:text>
<xsl:text>EP  - </xsl:text><xsl:value-of select="cr:last_page"/><xsl:text>&#xa;</xsl:text>
</xsl:template>

<xsl:template match="cr:contributors">
  <xsl:for-each select="cr:person_name[@contributor_role='author']">
 <xsl:text>AU  - </xsl:text><xsl:value-of select="concat(cr:given_name,' ',cr:surname)"/><xsl:text>&#xa;</xsl:text>
  </xsl:for-each>
  <xsl:for-each select="cr:person_name[@contributor_role='editor']">
<xsl:text>ED  - </xsl:text><xsl:value-of select="concat(cr:given_name,' ',cr:surname)"/><xsl:text>&#xa;</xsl:text>
  </xsl:for-each>
<xsl:text>AD  - </xsl:text><xsl:value-of select="cr:organization[1]"/><xsl:text>&#xa;</xsl:text>
 </xsl:template>

</xsl:stylesheet>
