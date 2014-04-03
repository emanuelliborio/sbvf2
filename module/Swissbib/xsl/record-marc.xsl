<?xml version="1.0" encoding="utf-8"?>
<xsl:stylesheet version="1.0" xmlns:xsl="http://www.w3.org/1999/XSL/Transform"
                              xmlns:marc="http://www.loc.gov/MARC21/slim"
                              xmlns:php="http://php.net/xsl"
                              exclude-result-prefixes="marc php">
  <xsl:output method="html" indent="yes"  />

  <xsl:template match="/">
    <xsl:apply-templates/>
  </xsl:template>

  <xsl:template match="marc:record">
      <table style="border: 0px;" class="citation">
        <tr>
          <th>LEADER</th>
          <td colspan="3"><xsl:value-of select="//marc:leader"/></td>
        </tr>
        <xsl:apply-templates select="marc:datafield|marc:controlfield"/>
      </table>
  </xsl:template>

  <xsl:template match="//marc:controlfield">
      <tr>
        <th style="text-align: right; vertical-align: top;">
          <xsl:value-of select="@tag"/>
        </th>
        <td colspan="3"><xsl:value-of select="."/></td>
      </tr>
  </xsl:template>

  <xsl:template match="//marc:datafield">
      <tr>
        <th style="text-align: right; vertical-align: top;">
          <xsl:value-of select="@tag"/>
        </th>
        <td><xsl:value-of select="@ind1"/></td>
        <td><xsl:value-of select="@ind2"/></td>
        <td>
          <xsl:apply-templates select="marc:subfield"/>
        </td>
      </tr>
  </xsl:template>

  <xsl:template match="marc:subfield">
      <strong>|<xsl:value-of select="@code"/></strong>&#160;<xsl:value-of disable-output-escaping="yes" select="php:function('\Swissbib\XSLT\MARCFormatter::compileSubfield', .)"/>&#160;
  </xsl:template>

</xsl:stylesheet>