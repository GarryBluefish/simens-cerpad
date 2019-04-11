RainbowBox
==========

RainbowBox is a Python 3 module for generating rainbow boxes, a novel technique
for visualizing overlapping sets or instanciation relations in ontologies.
RainbowBox produces an HTML / CSS / Javascript output from a conceptual description.

RainbowBox has been created by Jean-Baptiste Lamy (University Paris 13)
at the LIMICS research Laboratory.
RainbowBox is available under the GNU LGPL licence.

See the doc/ directory for examples of use (e.g. doc/aa.py for the amino-acid example).

In case of trouble, please contact Jean-Baptiste Lamy <jibalamy **@** free **.** fr>


Installation
------------

First untar the tarball.

RainbowBox uses Python's DistUtils for installation. To install, type (as root):

cd RainbowBox-*
python3 ./setup.py install

Dependencies: The following Python modules are required:

 * metaheuristic_optimizer (mandatory)

 * werkzeug (optional, for running RainBio web app)


For running RainBio locally, run in a terminal:

::
   
   python rainbowbox/appliweb_serveur/serveur.py

And then open the following URL: http://localhost:8080/static/appliweb/rainbio/index.html


Links
-----

RainbowBox on BitBucket (development repository): https://bitbucket.org/jibalamy/rainbowbox

Mail me for any comment, problem, suggestion or help !

Jiba -- Jean-Baptiste LAMY -- <jibalamy **@** free **.** fr>
