# Introduction #

Add your content here.


# Details #

Web services are great:<br><ul><li>would be good to make use of <a href='http://xissn.worldcat.org/webservices/xid/issn/0968-4468?method=getMetadata&amp;format=xml&amp;fl=*'>xISSN</a> but this requires an OCLC developer key or whatever they call it. Also need to be prepared for ISSNs from former titles being used for the continuing title! The OCLC xISSN service provides continuing<br>
titles data - very handy for this problem.<li>CrossRef - use the <a href='http://www.crossref.org/openurl/?id=doi:10.1007%2FBF02521816&amp;format=unixref&amp;redirect=false'>unixref</a>
option though - there's some nice metadata that way. If I was to start<br>
over, I'd use xpath with SimpleXML... much easier that way from what<br>
I've seen. CrossRef resolves forwards <b>and</b> backwards from metadata to a DOI. I've learnt that caching this data is fine too - see <span>Chuck Kocher's response re:caching <a href='http://search.twitter.com/search?q=%23Crossrefs'>#Crossrefs</a> unixref response: <a href='http://bit.ly/17MWcg'><a href='http://bit.ly/17MWcg'>http://bit.ly/17MWcg</a></a></span><li>Publishers<br>
often provide a DOI resolver of their own, you can't resolve it to<br>
metadata but it provides a stable link with that publisher - useful if<br>
their <a href='http://www3.interscience.wiley.com/resolve/openurl?issn=0196-4763&amp;genre=journal'>openurl resolver doesn't work</a><li>PubMed - <a href='http://hublog.hubmed.org/archives/001763.html'>Alf Eaton has a nice simple php script</a>

(I'd still use curl though for these and other simple services, for<br>
simple proxy support). NLM do a great job with their web services -<br>
they've gone to <a href='http://eutils.ncbi.nlm.nih.gov/entrez/query/static/esoap_help.html'>version 2</a> since I started using their data.</li><li>Biblio.net - not using it yet, but worth investigating for book data, and possibly journal titles too.</li><li><a href='http://ops.espacenet.com./index.html'>OPS</a> - <a href='http://ops.espacenet.com./examples.html'>Open Patent Services</a> are great if you're dealing with <b>patents</b>... I haven't found a way to get the WIPO data, but Espace does a pretty good job