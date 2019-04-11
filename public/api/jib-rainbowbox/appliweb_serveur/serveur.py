import sys, os, traceback



def application(env, start_response):
  path = env["PATH_INFO"]

  try:
    if   path.startswith("/appliweb/rainbio/"):
      import rainbowbox.appliweb_serveur.rainbio as rainbio
      return rainbio.application(env, start_response)
    
  except:
    html = """<html><body>Erreur :<br/><br/><pre>%s</pre></body></html>""" % traceback.format_exc()
    
    start_response('200 OK', [("Content-Type", "text/html; charset=utf-8"),
                              ("Cache-Control", "max-age=600")])
    return [html.encode("utf8")]
    
    
  html = """<html><body>Application non trouvée : %s !</body></html>""" % path
  start_response('200 OK', [("Content-Type", "text/html; charset=utf-8"),
                            ("Cache-Control", "max-age=600")])
  return [html.encode("utf8")]
   
  
if __name__ == "__main__":
  import rainbowbox.appliweb_serveur
  rainbowbox.appliweb_serveur.IS_LOCALHOST = True
  
  import werkzeug.serving
  werkzeug.serving.run_simple("localhost", 8080, application,
    static_files = { "/static/appliweb" : os.path.join(os.path.dirname(os.path.dirname(__file__)), "appliweb") })

