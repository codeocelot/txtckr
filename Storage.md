# Introduction #
What sort of database systems will be used?

txtckr will use the Vork framework, so MongoDB will be used as the txtckr base install (for internal storage, logging, etc). People can still use databases if they want for individual modules, etc.

# Details #
**MongoDB** - Preferred option.

MongoDB supports php objects, and it should work pretty well for the response from CrossRef, and the txtckr objects.
Mongo has the advantage of not needing to build SQL protection into textseeka - no noticeable overhead for inserting reference objects into Mongo, and easy integration with Windows and for **nix.**

**MySQL**

MySQL has been used in textseeka, but is problematic to comprehensively support all of the objects and responses for txtckr.

**PostGres**

This could work, but not sure about using XML - the database overhead, together with XML, although xpath could be used with PostGres