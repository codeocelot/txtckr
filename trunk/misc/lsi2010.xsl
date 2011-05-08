<?xml version="1.0" encoding="utf-8" ?>
<xsl:stylesheet xmlns:xsl="http://www.w3.org/1999/XSL/Transform" version="1.0">
  <xsl:output method="text" version="1.0" encoding="utf-8" indent="yes" />
<xsl:template match="SerialsSet">
<DATA>
<xsl:for-each select = "*">
    	<xsl:if test = "./Serial">
            <xsl:text>'</xsl:text><xsl:value-of select="./Serial[@DataCreationMethod]" /><xsl:text>',</xsl:text>
            <xsl:text>'</xsl:text><xsl:value-of select="./Serial[@Status]" /><xsl:text>',</xsl:text>
            <xsl:text>'</xsl:text><xsl:value-of select="./Serial[@PMC]" /><xsl:text>',</xsl:text>
        </xsl:if>
        <xsl:if test = "./Serial/NlmUniqueID">
            <xsl:text>'</xsl:text><xsl:value-of select="." /><xsl:text>',</xsl:text>
        </xsl:if>
        <xsl:if test = "./Serial/Title">
            <xsl:text>'</xsl:text><xsl:value-of select="." /><xsl:text>',</xsl:text>
        </xsl:if>
        <xsl:if test = "./Serial/MedlineTA">
            <xsl:text>'</xsl:text><xsl:value-of select="." /><xsl:text>',</xsl:text>
        </xsl:if>
        <xsl:if test = "./Serial/ISSNLinking">
            <xsl:text>'</xsl:text><xsl:value-of select="." /><xsl:text>',</xsl:text>
        </xsl:if>
        <xsl:choose>
            <xsl:when test = "./Serial/ContinuationNotes">
                <xsl:text>'</xsl:text><xsl:value-of select="." /><xsl:text>';&#xa;</xsl:text>
            </xsl:when>
            <xsl:otherwise>
                <xsl:text>'';&#xa;</xsl:text>
            </xsl:otherwise>
        </xsl:choose>
</xsl:for-each>
  </DATA>
</xsl:template>
</xsl:stylesheet>
