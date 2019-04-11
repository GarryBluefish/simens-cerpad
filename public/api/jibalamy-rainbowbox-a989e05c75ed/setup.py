#! /usr/bin/env python
# -*- coding: utf-8 -*-
# RainbowBox
# Copyright (C) 2015-2016 Jean-Baptiste LAMY
# LIMICS (Laboratoire d'informatique médicale et d'ingénierie des connaissances en santé), UMR_S 1142
# University Paris 13, Sorbonne paris-Cité, Bobigny, France

# This program is free software: you can redistribute it and/or modify
# it under the terms of the GNU Lesser General Public License as published by
# the Free Software Foundation, either version 3 of the License, or
# (at your option) any later version.

# This program is distributed in the hope that it will be useful,
# but WITHOUT ANY WARRANTY; without even the implied warranty of
# MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
# GNU Lesser General Public License for more details.

# You should have received a copy of the GNU Lesser General Public License
# along with this program.  If not, see <http://www.gnu.org/licenses/>.

import os, os.path, sys, glob, distutils.core

HERE = os.path.dirname(sys.argv[0]) or "."

if len(sys.argv) <= 1: sys.argv.append("install")

data_files = [
  ("rainbowbox",
  ["rainbowbox.css", "rainbowbox.js"]),
  ]

distutils.core.setup(
  name         = "RainbowBox",
  version      = "0.1",
  license      = "LGPLv3+",
  description  = "A visualization module for generating rainbow boxes, a novel technique for visualizing overlapping sets.",
  long_description = open(os.path.join(HERE, "README.rst")).read(),
  
  author       = "Lamy Jean-Baptiste (Jiba)",
  author_email = "<jibalamy *@* free *.* fr>",
  url          = "https://bitbucket.org/jibalamy/rainbowbox",
  classifiers  = [
    "Development Status :: 4 - Beta",
    "Intended Audience :: Developers",
    "Intended Audience :: Science/Research",
    "Programming Language :: Python :: 3",
    "License :: OSI Approved :: GNU Lesser General Public License v3 or later (LGPLv3+)",
    "Operating System :: OS Independent",
    "Environment :: Web Environment",
    "Topic :: Scientific/Engineering :: Human Machine Interfaces",
    "Topic :: Scientific/Engineering :: Visualization",
    "Topic :: Software Development :: User Interfaces",
    "Topic :: Software Development :: Libraries :: Python Modules",
    ],
  
  package_dir  = {"rainbowbox" : "."},
  packages     = ["rainbowbox"],
  package_data = {"rainbowbox" : ["rainbowbox.js", "rainbowbox.css"]},
  
  data_files = data_files,
  )
