CREATE TABLE "{prefix}_profile_data" (
  "uid" int NOT NULL,
  PRIMARY KEY  ("uid")
);

CREATE TABLE "{prefix}_profile_definitions" (
  "field_id" serial,
  "field_name" varchar(32) NOT NULL,
  "label" varchar(255) NOT NULL,
  "type" varchar(16) NOT NULL,
  "validation" varchar(255) NOT NULL,
  "required" smallint NOT NULL,
  "show_form" smallint NOT NULL,
  "weight" smallint NOT NULL,
  "description" text NOT NULL,
  "access" text NOT NULL,
  "options" text NOT NULL,
  PRIMARY KEY  ("field_id")
);
