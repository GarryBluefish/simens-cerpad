
# http://localhost:8080/appliweb/rainbio/index.html
# http://www.lesfleursdunormal.fr/static/appliweb/rainbio/index.html

from functools import lru_cache
import cgi

import rainbowbox.appliweb_serveur
from rainbowbox.rainbio import *

@lru_cache(maxsize=32)
def dataset_2_html_page(dataset):
  genes, gene_lists = reads_interactivenn(dataset)
  
  if not appliweb_serveur.IS_LOCALHOST:
    if (len(gene_lists) > 15) or (len(genes) > 40000): return None
  
  html_page = gene_lists_2_html_page(genes, gene_lists, static_url = "../../static/appliweb/rainbio/")
  html_page.javascripts.append("/static/appliweb/brython.js")
  html_page.javascripts.append("/static/appliweb/brython_stdlib.js")
  
  return html_page.get_html_with_header()


def application(env, start_response):
  path = env["PATH_INFO"][17:]
  
  formdata = cgi.FieldStorage(environ = env, fp = env['wsgi.input'])
  
  if (not "dataset" in formdata) or (not formdata["dataset"].filename):
    start_response('301 Moved', [('Location', "/static/appliweb/rainbio/index.html")])
    return []
  
  file_data = formdata["dataset"].file.read()
  
  if not file_data:
    start_response('301 Moved', [('Location', "/static/appliweb/rainbio/index.html")])
    return []
  
  html = dataset_2_html_page(file_data)
  
  if not html:
    html = """<html><body>
RainBio is currently limited to 15 sets and 40000 elements (<i>e.g.</i> genes)
</body></html>"""
    
  start_response('200 OK', [("Content-Type", "text/html; charset=utf-8"),
                            ("Cache-Control", "max-age=600")])
  return [html.encode("utf8")]


