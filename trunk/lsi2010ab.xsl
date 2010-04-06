<?xml version="1.0" encoding="utf-8" ?>
<xsl:stylesheet xmlns:xsl="http://www.w3.org/1999/XSL/Transform" version="1.0">
  <xsl:output method="text" version="1.0" encoding="utf-8" indent="yes" />
<xsl:template match="/">
<DATA>
<xsl:for-each select = "SerialsSet/Serial">
<xsl:variable name="issnL"><xsl:value-of select="ISSNLinking" /></xsl:variable>
<xsl:variable name="nlmID"><xsl:value-of select="NlmUniqueID" /></xsl:variable>
    <xsl:for-each select = "CrossReferenceList">
        <xsl:for-each select = "CrossReference">
                <xsl:value-of select="$nlmID" /><xsl:text>&#x9;</xsl:text>
                <xsl:value-of select="$issnL" /><xsl:text>&#x9;</xsl:text>
                <xsl:value-of select="@XrType" /><xsl:text>&#x9;</xsl:text>
                <xsl:value-of select="XrTitle" /><xsl:text>&#xa;</xsl:text>  
        </xsl:for-each>
    </xsl:for-each>
    <xsl:value-of select="$nlmID" /><xsl:text>&#x9;</xsl:text>
    <xsl:value-of select="$issnL" /><xsl:text>&#x9;</xsl:text>
    <xsl:text>I</xsl:text><xsl:text>&#x9;</xsl:text>
    <xsl:value-of select="ISOAbbreviation" /><xsl:text>&#xa;</xsl:text>
</xsl:for-each>    
  </DATA>
</xsl:template>
</xsl:stylesheet>
