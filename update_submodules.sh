#!/bin/sh
git submodule sync --recursive
git submodule update --remote --merge
git submodule foreach --recursive git submodule update --init --recursive
