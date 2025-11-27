<?php

namespace Sal\Seven\Model;

enum ContentType: string
{
    case JSON = 'application/json';
    case JSON_MERGE_PATCH = 'application/merge-patch+json';
    case X_WWW_FORM_URLENCODED = 'application/x-www-form-urlencoded';
    case XML = 'application/xml';
    case HTML = 'text/html';
    case TEXT = 'text/plain';
    case CSV = 'text/csv';
    case PDF = 'application/pdf';
    case JPEG = 'image/jpeg';
    case PNG = 'image/png';
    case GIF = 'image/gif';
    case ZIP = 'application/zip';
    case TAR = 'application/x-tar';
    case GZIP = 'application/gzip';
    case BZIP2 = 'application/x-bzip2';
    case BZIP = 'application/x-bzip';
    case GZ = 'application/gz';
    case BZ2 = 'application/bz2';
    case TARGZ = 'application/x-tgz';
    case TBZ2 = 'application/x-tbz2';
    case XZ = 'application/x-xz';
    case XZ2 = 'application/x-xz2';
}
