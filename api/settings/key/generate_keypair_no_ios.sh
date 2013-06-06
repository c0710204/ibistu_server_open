#!/bin/sh
################################################################################
# This file is part of CastleCrypt
################################################################################
#
# (C) Copyright 2012, Joseph Wessner <castleCrypt@hdr.meetr.de>
#
# CastleCrypt is free software; you can redistribute it and/or modify
# it under the terms of the GNU Lesser General Public License as published by
# the Free Software Foundation; either version 3 of the License, or
# (at your option) any later version.
#
# This program is distributed in the hope that it will be useful,
# but WITHOUT ANY WARRANTY; without even the implied warranty of
# MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
# GNU Lesser General Public License for more details.
#
# You should have received a copy of the GNU Lesser General Public
# License along with this program; if not, If not,
# see <http://www.gnu.org/licenses/>.
#
################################################################################

# This file generates a new private/public Key pair in both DER and PEM formats.
#
# Usage:
# generate_keypair.sh <name>
#

# Path to openssl binary
OPENSSL="openssl"
# KeySize (default: 2048) 
# ! do not change this value as CastleCrypt only supports 2048 Bit keys now !
KEYSIZE=2048

if [ -z "$1" ]; then
	echo "Usage: $0 <name>";
	exit 1;
fi

# Generate private key
$OPENSSL genrsa -out $1"_privateKey.pem" $KEYSIZE && \
$OPENSSL pkcs8 -topk8 -nocrypt -in $1"_privateKey.pem" -outform der -out $1"_privateKey.der" && \

# Export public keys
$OPENSSL rsa -in $1"_privateKey.pem" -pubout -outform PEM -out $1"_publicKey.pem" && \
$OPENSSL rsa -in $1"_privateKey.pem" -pubout -outform DER -out $1"_publicKey.der" && \
cp $1"_publicKey.der" /home/www/m.bistu.edu.cn/web/$1"_publicKey.der"
cp $1"_publicKey.pem" /home/www/m.bistu.edu.cn/web/$1"_publicKey.pem"
echo "--> keys $1 generated"
