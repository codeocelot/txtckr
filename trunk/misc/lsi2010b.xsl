<?xml version="1.0" encoding="utf-8" ?>
<xsl:stylesheet xmlns:xsl="http://www.w3.org/1999/XSL/Transform" version="1.0">
  <xsl:output method="text" version="1.0" encoding="utf-8" indent="yes" />
<xsl:template match="/">
<DATA>
<xsl:for-each select = "SerialsSet/Serial">
            <xsl:text>'</xsl:text><xsl:value-of select="@DataCreationMethod" /><xsl:text>',</xsl:text>
            <xsl:text>'</xsl:text><xsl:value-of select="@Status" /><xsl:text>',</xsl:text>
            <xsl:text>'</xsl:text><xsl:value-of select="@PMC" /><xsl:text>',</xsl:text>
            <xsl:text>'</xsl:text><xsl:value-of select="NlmUniqueID" /><xsl:text>',</xsl:text>
            <xsl:text>'</xsl:text><xsl:value-of select="translate (Title, ''', '`')" /><xsl:text>',</xsl:text>
            <xsl:text>'</xsl:text><xsl:value-of select="MedlineTA" /><xsl:text>',</xsl:text>
            <xsl:text>'</xsl:text><xsl:value-of select="ISSNLinking" /><xsl:text>',</xsl:text>
            <xsl:text>'</xsl:text><xsl:value-of select="ContinuationNotes" /><xsl:text>';&#xa;</xsl:text>
</xsl:for-each>
  </DATA>
</xsl:template>
</xsl:stylesheet>
