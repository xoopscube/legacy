<?php
/*
 * Created on 2007/05/28 by nao-pon http://hypweb.net/
 * $Id: import.lng.php,v 1.1 2010/03/06 08:20:30 nao-pon Exp $
 * Thanks bokanta :-D
 */

$msg = array(
    'title_import_dir' => 'Selection of the module to be imported',
    'import_dir' => 'Choose the module (i.e. directory name) to be imported.',
    'target_page' => 'Selected pages',
    'target_page_sel' => 'of $from',
    'target_page_all' => 'All pages of $from',
    'target_page_note' => 'Split with \'&\' for multiple choices (NOTE: All pages in the sub-directories are included).<br />All the pages will be imported unless specified.',
    'title_select_option' => 'Selection of the import options',
    'target_module' => 'The module to be imported: ',
    'keep_pgid' => 'Page ID',
    'keep_pgid_1' => 'Keep the page ID of $from',
    'keep_pgid_1_note' => '(All the existing pages will be deleted.)',
    'keep_pgid_2' => 'Keep the page ID of $to',
    'keep_pgid_2_note' => '(Specify the options below.)',
    'keep_page' => 'Keep the original page name',
    'keep_page_1' => 'Overwrite $to with $from',
    'keep_page_2' => 'Keep the pages of $to (no import)',
    'invalid_option' => 'Invalid option(s)',
    'title_do_import' => 'Final confirmation before executing import',
    'title_no_files' => 'The pages to be imported do not exist.',
    'title_do_check' => 'Check the contents to be imported',
    'do_check_note' => 'The files to be imported will be checked in the next step, which may take some time.<br />Please wait after clicking [Check the files to be imported].',
    'btn_do_next' => 'Next step',
    'btn_go_first' => 'Go back to start',
    'btn_do_check' => 'Check the files to be imported',
    'btn_do_copy' => 'Execute the import (Copy the selected files)',
    'do_copy_note' => 'The above-listed files will be copied from $from to $to. In case PHP-execution time-out should occur because the numbers and/or sizes of files exceed the limit, a dialog for confirming whether the execution to be continued. Please  continue the exectuion.',
    'do_copy_nothing' => 'The files to be imported do not exist.',
    'title_convert' => 'Wiki format conversion',
    'do_convert' => 'Convert the Wiki format',
    'do_convert_note' => 'The PukiWiki 1.3 format will be converted to the PukiWiki 1.4 format.',
    'do_convert_wiki'     => "
** Execution contents
:Changing the format of the definition lists|
'': :'' is changed to '': |''.
:Splitting nestable block elements|
Inserting a blank line after a nestable block element to prevent the following elements from being its child elements
:Tilde \"~\" on the back of list elements|
When a tilde \"~\" appears on the back of \"-/+\" at the begininng of a line,
a space is inserted to prevent the tilde from being the format for a new line.
:Converting plug-in s.|
--&#35;category() -> &#38;tag();
--&#35;attacheref( -> &#35;ref(
--&#38;attacheref( -> &#38;ref(
:Converting the page contents|
The page contenst of PukiWikiMod is converted to those in the xpWiki format.
 
** Note
It may take severla minutes to complete the conversion.  Please be patient after clicking the execution button.
 
In case PHP-execution time-out should occur, a dialog for confirming whether the execution to be continued. Please  continue the exectuion.
 
** Execution of conversion
Please click the [Convert the Wiki format] button.
",
    'msg_all_done' => 'Import is completed successfully. Please proceed to the database synchronization.',
    'msg_exec'    => "* File names were checked.\n No errors are found.\n\n Please click [[Execute>%s]] for continuing the conversion\n",
    'msg_error'   => "* File names were checked.\n Errors are found and the file name conversion is terminated. \n Please re-execute the converion after fixing the errors\n",
    'msg_done'    => '* The file name conversion is completed.',
    'err_writable' => '** No files nor directories are found and/or not writable.',
    'err_already' => '** A file with the same name already exists.',
    'err_invalid' => '** The page name is not allowed for PukiWiki 1.4.',
    'err_no_from_dir' => 'The directory to be imported is not found.',
    'err_no_to_dir' => 'The directory to be exported is not found.',
    'err_writable_to' => 'The directory to be exported is not writable.',
    'more_copy_note' => 'The process is paused because of the time-out error during the file copy process.<br />Please click [Continue], and ',
    'more_convert_note' => 'The process is puased because of the time-out error during the format convesion.<br />[Please click [Continue], and ',
    'title_do_more' => 'the rest of process will be continued.',
    'do_more' => 'Remaining $count files will be converted',
    'btn_do_more' => 'Contiune',
); 
?>
