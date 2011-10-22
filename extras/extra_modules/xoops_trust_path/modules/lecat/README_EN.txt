Lecat
=====
Leimg is category management module for other modules.
Module developers can use this module for handle category and permission control.
Module developer can get category tree and can check permission about user group, category id and action(view/edit/manage/etc.)

Environment
-----------
XOOPS Cube Legacy 2.2 or later.

Setup
-----
You must make at least one category.

Main Feature
------------
- Category management(create/edit/list/view/delete)
- Manage tree like category.
- Permission management about each category, inherit parent's permission to descendant categories. A descendant category can overwrite these permission.

Client Module
-------------
Modules using this category management functions are called "(category) client (module)".
Client modules must implement Legacy_iCategoryClientDelegate interface in (html)/modules/legacy/class/interface/CatClientDelegateInterface.class.php

Then, they can use this module's delegate functions of Lecat_DelegateFunctions class in (trust_path)/modules/lecat/class/DelegateFunctions.class.php.
  Legacy_Category.(dirname).GetTitle
  Legacy_Category.(dirname).GetTree
  Legacy_Category.(dirname).GetTitleList
  Legacy_Category.(dirname).HasPermission
  Legacy_Category.(dirname).GetParent
  Legacy_Category.(dirname).GetChildren
  Legacy_Category.(dirname).GetCatPath
  Legacy_Category.(dirname).GetPermittedIdList


Update History
--------------
ver 2.01
- Check client data existence before delete the category.

