<?xml version="1.0" encoding="utf-8" ?>
<xsl:stylesheet xmlns:xsl="http://www.w3.org/1999/XSL/Transform" version="1.0">
  <xsl:output method="text" version="1.0" encoding="utf-8" indent="yes" />
<xsl:template match="/">
<DATA>
<xsl:text>
CREATE TABLE `lsi2010` (
  `DataCreationMethod` varchar(1) collate utf8_unicode_ci NOT NULL,
  `Status` varchar(25) collate utf8_unicode_ci NOT NULL,
  `PMC` enum('','Yes','No') collate utf8_unicode_ci NOT NULL,
  `NlmUniqueID` varchar(25) collate utf8_unicode_ci NOT NULL,
  `Title` varchar(2048) collate utf8_unicode_ci NOT NULL,
  `MedlineTA` varchar(56) collate utf8_unicode_ci NOT NULL,
  `ISSNLinking` varchar(9) collate utf8_unicode_ci NOT NULL,
  `ISOAbbreviation` varchar(1024) collate utf8_unicode_ci NOT NULL,
  `ContinuationNotes` varchar(2048) collate utf8_unicode_ci NOT NULL,
  UNIQUE KEY `NlmUniqueID` (`NlmUniqueID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;&#xa;
</xsl:text>
<xsl:text>INSERT INTO `lsi2010` (`DataCreationMethod`, `Status`, `PMC`, `NlmUniqueID`, `Title`, `MedlineTA`, `ISSNLinking`, `ISOAbbreviation`, `ContinuationNotes`) VALUES &#xa;</xsl:text>
<xsl:for-each select = "SerialsSet/Serial">
            <xsl:text>('</xsl:text><xsl:value-of select="@DataCreationMethod" /><xsl:text>',</xsl:text>
            <xsl:text>'</xsl:text><xsl:value-of select="@Status" /><xsl:text>',</xsl:text>
            <xsl:text>'</xsl:text><xsl:value-of select="@PMC" /><xsl:text>',</xsl:text>
            <xsl:text>'</xsl:text><xsl:value-of select="NlmUniqueID" /><xsl:text>',</xsl:text>
            <xsl:text>'</xsl:text><xsl:value-of select="Title" /><xsl:text>',</xsl:text>
            <xsl:text>'</xsl:text><xsl:value-of select="MedlineTA" /><xsl:text>',</xsl:text>
            <xsl:text>'</xsl:text><xsl:value-of select="ISSNLinking" /><xsl:text>',</xsl:text>
            <xsl:text>'</xsl:text><xsl:value-of select="ISOAbbreviation" /><xsl:text>',</xsl:text>
            <xsl:text>'</xsl:text><xsl:value-of select="ContinuationNotes" /><xsl:text>'),&#xa;</xsl:text>
</xsl:for-each>
  </DATA>
</xsl:template>
</xsl:stylesheet>
