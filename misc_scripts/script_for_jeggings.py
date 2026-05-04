#######################################################################################################################
#######################------IMPORTANT!!!PLEASE CHECK FIXED SECTION!!!!IMPORTANT---####################################
#######################################################################################################################
#######################################################################################################################
import os
import sys
import csv
import MySQLdb.cursors
from MySQLdb.constants import FIELD_TYPE
import copy
import time
import re

my_conv = { FIELD_TYPE.LONG: str } # CONVERTS THE LONG DATA TYPE
# RETURNED FROM SQL TABLES AS INTEGERS


#######################################################################################################################
#######################################################################################################################
#######################################################################################################################
#######################################################################################################################
#########################-----------------FIXED SECTION----------------------------####################################
#######################################################################################################################
#######################################################################################################################
#######################################################################################################################
manufacturer_code =	"TES_JP"																			###############
																										###############	
whether_img_folder  = 0	# 1 tells whether the images are in sku folders								###############
							# 0 for no image folder														###############	
																										###############

											
################################################################
###--- a is the dictionary of FIXED fields in the CSV files---##
###---Columns in CSV should be in this order-----------------###
################################################################
a =  { 1 : 'name',
2 : 'set_description',
3 : 'description',
4 : 'model',
5 : 'sku',
6 : 'price', 
7 : 'piece_in_set',
8 : 'quantity',
9 : 'minimum',
10 : 'weight',
11 : 'price_per_set',
12 : 'status',
13 : 'shipping',
14 : 'category_id',
15 : 'language_id',
16 : 'commission',
17 : 'tax_class_id',
18 : 'seller_tax',
19 : 'subtract',
20 : 'tag',
21 : 'meta_keyword', 
22 : 'meta_title',
23 : 'meta_description',
24 : 'date_added',
25 : 'image',
26 : 'weight_class_id',
27 : 'stock_status_id',
28 : 'seller_id',
29 : 'product_status',
30 : 'product_approved',
31 : 'sort_order'}

#########################################################################
###-- b is the list of VARIABLE fields in the CSV files e.g Filters---###
#########################################################################

# b = ['Fabric Type','Size Set / Color Set', 'Style','Work Type']
# b = ['Fabric Type','Set Type','Length','Sleeve' ,'Work Type', 'Neck Type', 'Product Type']
b = ['Fabric Type','Size Set / Color Set']

first_index = max(a.keys()) # index of first column in list
last_index = first_index +len(b) # index of last column in list 										
																										###############
																										###############
																										###############
#######--XXXXXXXXXXXXXXXXXXXXXXX----USE--------XXXXXXXXXXXXXXXXXXXXX--###############					###############
####---------Seller Tax- 5------------------------------------------#################					###############
####------- Tax Class ID - Stitchted Apparels - 9-------------------#################					###############
####---------- Commission- 4------------------------------------------###############					###############
####---------category_id- 66 : Salwar																	###############
####---------category_id- 61 : Kurti																	###############
####---------category_id- 68 : dresses 																	###############
####---------category_id- 66																			###############
####---------category_id- 66																			###############
####---------category_id- 66																			###############
																										###############	
TAX_CLASS = 9 		# Tax class id																		###############
SELL_TAX = 5		# Seller Tax																		###############
COMM = 5			# Commission																		###############																					
CAT_ID = 77			# Category id	
Parent_cat = 76		# Parent Category Id																###############

### ------The imagee roots are image paths for image to be upload-----### 								###############
### ------The server_image_root is for image directory in server -----###								###############
### ------The local_image_root is for image directory in local machine ----###							###############	
																										###############
server_image_root = "catalog/"+manufacturer_code+"/jegging/"											###############
local_image_root = "/Users/qurius/Documents/" +manufacturer_code+"/jegging/"							###############
																										###############
																										###############
#######################################################################################################################
#######################################################################################################################
#######################################################################################################################
#######################################################################################################################
#########################---------------END FIXED SECTION----------------------------##################################
#######################################################################################################################
#######################################################################################################################
#######################################################################################################################

######################################################################
###---CONNECTION VARIRABLE; CHANGE THE DB NAME AND OTHER VALUES HERE##
######################################################################
cnx = MySQLdb.connect(user= 'root',passwd = 'p341987g',host = 'localhost',db = 'tes_jp', conv = my_conv)

################################################################
###-- opencsv for reading csv and returning a reader object---##
################################################################

filetobeuploaded = sys.argv[1] # Takes the argument as the csv file 
def opencsv(filename):
	fopen = open(filename, 'r')
	reader = csv.reader(fopen, delimiter=',')
	return(reader)
	
reader1 = opencsv(filetobeuploaded)
product_list_dict= [] # INTERIM VARIABLE TO CREATE MAIN DICTIONARY
product_list_variable = [] #LIST OF LIST CONTAINING THE VARIABLE value WHERE b(see above) is the keys
product_dict = {}

################################################################
###-- create_dict for creating a dictionary of fixed fields---##
###-- CSV and returning a reader List of Dictionaries---------##
################################################################


def create_dict(reader):  #to get the reader pointer and create list of dictionaries
	
	rownum = 0                                            
	for row in reader:
		#break if empty    	
		if not row:
			print "Empty row entered"
			sys.exit()
			break
		#save header row			
		if rownum == 0:
			header = a.values()
			rownum += 1
		else:					 
			rownum += 1		
			if len(header) != len(row[0:first_index]):
 				print "element missing in row "
 				print len(header)
 				print len(row)
				break
			product_dict = {}
			for key,value in zip(header, row[0:first_index]):
				product_dict[key] = value.strip(" ")
			product_list_dict.append(product_dict)
			if len(b) != len(row[first_index:last_index]):
				print "ERROR : Please check the variable headers"
			product_list_variable.append(row[first_index:last_index])
	
	return(product_list_dict)	

dictionary_from_CSV = create_dict(reader1) # MAIN DICTIONARY


################################################################
###--For checking important fields in the dictionary----------##
################################################################

# for i,v in enumerate(dictionary_from_CSV):
# 	for elements in v:
# 		if not 'model' in v.keys():
# 			print "No model number field.Model number is compulsory. See your CSV"
# 			sys.exit()
# 			
# 
# 		if not v['model']:
# 			print "Model number is empty for row {}. See your CSV".format(i+1)
# 			sys.exit()
# 		
# 		
# 		if not 'name' in v.keys():
# 			print "No Product name field .Product name is compulsory. See your CSV"
# 			sys.exit()
# 		
# 		
# 		if not v['name']:
# 			print "Empty name found in row{}.Product name cannot be empty".format(i+1)
# 			sys.exit()	
# 			
# 		
#  		if not 'category_id' in v.keys():
# 			print "No category id field .category id  is compulsory. See your CSV"
# 			sys.exit()
# 		
# 		
# 		if not v['category_id']:
# 			print "Category id empty in row {}.Category Id cannot be empty".format(i+1)
# 			sys.exit()	
# 			
# 			
# 		if not 'language_id' in v.keys():
# 			print "No langugage id field.Product name is compulsory. See your CSV"
# 			sys.exit()
# 		
# 		
# 		if not v['language_id']:
# 			print "Language id empty in row {}.Language Id cannot be empty".format(i+1)
# 			sys.exit()	
# 			
# 		if not 'date_added' in v.keys():
# 			print "No date_added field. date_added is compulsory field. Add to your CSV"
# 			sys.exit()




######################################################################
###---SQL QUERY BLOCK where are sqls are stored for getting fields--##
###---from the tables in the MYSQL  ------------------------------####
######################################################################

sql_for_product_table = '''

	SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE table_name = 'oc_product'

'''
sql_for_product_description_table = '''

	SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE table_name = 'oc_product_description'

'''
sql_for_product_attribute_table = '''

	SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE table_name = 'oc_product_attribute'

'''
sql_for_product_discount_table = '''

	SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE table_name = 'oc_product_discount'

'''
sql_for_product_to_category = '''

	SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE table_name = 'oc_product_to_category'

'''
sql_for_product_filter_table = '''
	
	INSERT INTO oc_product_filter (product_id, filter_id) VALUES (%(product_id)s, %(filter_id)s)		

'''
sql_for_product_filter_description_table = '''

	SELECT filter_id, filter_group_id, name FROM  oc_filter_description

'''
sql_for_product_filter_group_description_table = '''

	SELECT filter_group_id, name FROM  oc_filter_group_description

'''
sql_for_product_image_table = '''
	
	INSERT INTO oc_product_image (product_id, image, sort_order) VALUES (%(product_id)s, %(image)s, %(sort_order)s)		

'''
sql_for_product_to_store_table = '''
	
	INSERT INTO oc_product_to_store (product_id, store_id) VALUES (%(product_id)s, %(store_id)s)		

'''
sql_for_multiseller_product_table = '''

	SELECT COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE table_name = 'oc_ms_product'

'''
sql_for_seo_url = '''
	
	INSERT INTO oc_url_alias (query, keyword) VALUES (%(query)s, %(keyword)s)		

'''
sql_for_product_to_category_table = '''
	
	INSERT INTO oc_product_to_category (product_id, category_id) VALUES (%(product_id)s, %(category_id)s)		

'''

########################################################################
###---CURSOR BLOCK where CURSORS are initialized for query execution--##
########################################################################

cursor_product_table = cnx.cursor(cursorclass = MySQLdb.cursors.DictCursor)
cursor_product_description_table = cnx.cursor(cursorclass =MySQLdb.cursors.DictCursor)
cursor_product_attribute_table = cnx.cursor(cursorclass =MySQLdb.cursors.DictCursor)
cursor_product_discount_table = cnx.cursor(cursorclass =MySQLdb.cursors.DictCursor)
cursor_product_category_table = cnx.cursor(cursorclass =MySQLdb.cursors.DictCursor)
cursor_filter_description_table = cnx.cursor(cursorclass =MySQLdb.cursors.DictCursor)
cursor_filter_group_description_table = cnx.cursor(cursorclass =MySQLdb.cursors.DictCursor)
cursor_filter_table = cnx.cursor(cursorclass =MySQLdb.cursors.DictCursor)
cursor_oc_filter_table = cnx.cursor(cursorclass =MySQLdb.cursors.DictCursor)
cursor_oc_filter_description_table = cnx.cursor(cursorclass =MySQLdb.cursors.DictCursor)
cursor_product_image_table = cnx.cursor(cursorclass =MySQLdb.cursors.DictCursor)
cursor_product_to_store_table = cnx.cursor(cursorclass =MySQLdb.cursors.DictCursor)
cursor_ms_product_table = cnx.cursor(cursorclass =MySQLdb.cursors.DictCursor)
cursor_url_alias_table = cnx.cursor(cursorclass =MySQLdb.cursors.DictCursor)
cursor_product_parent = cnx.cursor(cursorclass =MySQLdb.cursors.DictCursor)

cursor_var_1 =  cnx.cursor(cursorclass =MySQLdb.cursors.DictCursor)
cursor_var_2 =  cnx.cursor(cursorclass =MySQLdb.cursors.DictCursor)
######################################################################
###---SQL QUERY EXECUTION BLOCK where are sqls are executed -----##### 
######################################################################
              
cursor_product_table.execute(sql_for_product_table)
cursor_product_description_table.execute(sql_for_product_description_table)
cursor_product_attribute_table.execute(sql_for_product_attribute_table)
cursor_product_discount_table.execute(sql_for_product_discount_table)
cursor_product_category_table.execute(sql_for_product_to_category)
cursor_filter_description_table.execute(sql_for_product_filter_description_table)
cursor_filter_group_description_table.execute(sql_for_product_filter_group_description_table)
cursor_ms_product_table.execute(sql_for_multiseller_product_table)
# cursor_url_alias_table.execute(sql_for_seo_url)

######################################################################
###---Listing the result set from various tables and storing--------##
######################################################################

rows_product_table = list(cursor_product_table.fetchall())
rows_product_attribute_table = list(cursor_product_attribute_table.fetchall())
rows_product_discount_table = list(cursor_product_discount_table.fetchall())
rows_product_description_table = list(cursor_product_description_table.fetchall())
rows_product_category_table = list(cursor_product_category_table.fetchall())
rows_cursor_filter_description_table = list(cursor_filter_description_table.fetchall())
rows_cursor_filter_group_description_table = list(cursor_filter_group_description_table.fetchall())
rows_cursor_ms_product_table = list(cursor_ms_product_table.fetchall())

###########################################################################
###---DEF BLOCK where are definitions for further process are mentioned--##
###########################################################################

def table_struct_list_key(resultsetlist_from_table,key):
	structure_as_list = []
	for i, v in enumerate(resultsetlist_from_table):			
			if key in v.keys():
				structure_as_list.append(v[key])
	return(structure_as_list)# list [] of imported structure data

def table_struct_list(resultsetlist_from_table):
	structure_as_list = []
	for i, v in enumerate(resultsetlist_from_table):
		for elements in v:
			structure_as_list.append(v[elements])
	return(structure_as_list)# list [] of imported structure data

def columns_in_csv(resultsetlist, originalcsvlistdict):
	structure_list = table_struct_list(resultsetlist)	
	lop= []												
	for i, v in enumerate(originalcsvlistdict):
		if i == 0:	
			for elements in v:
				if elements in structure_list:
					lop.append(elements)
	new = set(structure_list)&set(lop)
	new_list = list(new)
	return(new_list)	# returns the list of keys from 
	# original dictionary common in product structure list 

def editeddictlist(resultsetlist, originalcsvlistdict):
	originalcsvduplicate = copy.deepcopy(originalcsvlistdict)
	structure_list = table_struct_list(resultsetlist)
		
	for i,v in enumerate(originalcsvduplicate):
		for elements in v.keys():
			if not elements in structure_list:
				del v[elements]
	
	return(originalcsvduplicate)# edited dictionary list RETURNS 
# MAIN DICIONARY WITH ONLY COMMON KEYS for only those items which are present in CSV 
# and table structure WHERE resultsetlist is the list from table structure

##############################################################################
###---DYNAMIC INSERT QUERY generation on the basis of common columns -----####
###---in CSV file and the related table structure ------------------------####
##############################################################################

def insert_for_table_with_product_id(table_name, table_structure_list_fromsql):
	str = "INSERT into {} (".format(table_name)
	str_values = "VALUES("
	product_struct_list = columns_in_csv(table_structure_list_fromsql,dictionary_from_CSV)
	for i in range (len(product_struct_list)-1):
		str=str+product_struct_list[i]+","
		str_values =  str_values + "%("+product_struct_list[i]+")s,"
			
	str=str+" "+product_struct_list[len(product_struct_list)-1]+","+ "product_id"
	str_values =  str_values + "%("+product_struct_list[len(product_struct_list)-1]+")s,"+"%(product_id)s"	
					
	
	str = str+ ")"+" " + str_values +")"
	return(str) #Insert Query for looping
	#from columns_in_csv table with datetime stamp
		
def insert_for_table(table_name, table_structure_list_fromsql):
	str = "INSERT into {} (".format(table_name)
	str_values = "VALUES("
	product_struct_list = columns_in_csv(table_structure_list_fromsql,dictionary_from_CSV)
	for i in range (len(product_struct_list)-1):
		str=str+product_struct_list[i]+","
		str_values =  str_values + "%("+product_struct_list[i]+")s,"
			
	str=str+product_struct_list[len(product_struct_list)-1]
	str_values =  str_values + "%("+product_struct_list[len(product_struct_list)-1]+")s"
					
	
	str = str+ ")"+" " + str_values +")"
	return(str) #Insert Query for looping
	#from columns_in_csv table

def insert_for_table_dummy(table_name, table_structure_list_fromsql):
	str = "INSERT into {} (".format(table_name)
	str_values = "VALUES("
	product_struct_list = columns_in_csv(table_structure_list_fromsql,dictionary_from_CSV)
	
	if len(product_struct_list) == 0:
		print "nothing common in csv and table structure"
		return
	if len(product_struct_list) == 1:
		str = str + product_struct_list[0]
		str_values = str_values + "%("+product_struct_list[0]+")s"
	else:
		for i in range (len(product_struct_list)-1):
			str=str+product_struct_list[i]+","
			str_values =  str_values + "%("+product_struct_list[i]+")s,"
			
		str=str+" "+product_struct_list[len(product_struct_list)-1]+","+ "category_id"
		str_values =  str_values + "%("+product_struct_list[len(product_struct_list)-1]+")s,"+"%(category_id)s"	
						
	
	str = str+ ")"+" " + str_values +")"
	return(str) #Insert Query for looping
	#from columns_in_csv table dummy
	
##############################################################################
###---List of Dictionaries with different keys as table structure elements--## 
##############################################################################		
	
edited_rows_product_table_dictlist = editeddictlist(rows_product_table,dictionary_from_CSV)
edited_rows_product_discount_table_dictlist = editeddictlist(rows_product_discount_table,dictionary_from_CSV)
edited_rows_product_attribute_table_dictlist = editeddictlist(rows_product_attribute_table,dictionary_from_CSV)
edited_rows_product_description_table_dictlist = editeddictlist(rows_product_description_table,dictionary_from_CSV)
edited_rows_product_category_table_dictlist = editeddictlist(rows_product_category_table,dictionary_from_CSV)
edited_rows_ms_product_table_dictlist = editeddictlist(rows_cursor_ms_product_table,dictionary_from_CSV)

##############################################################################
###--Generating SQL queries dynamically with the def insert-----------------## 
##############################################################################

upload_to_product_table_query = insert_for_table("oc_product", rows_product_table) 
upload_to_product_description_table_query= insert_for_table_with_product_id("oc_product_description", rows_product_description_table) 
upload_to_product_attribute_table_query = insert_for_table_with_product_id("oc_product_attribute", rows_product_attribute_table) 
upload_to_product_discount_table_query= insert_for_table_with_product_id("oc_product_discount", rows_product_discount_table) 
upload_to_product_category_table_query= insert_for_table_with_product_id("oc_product_to_category", rows_product_category_table) 
upload_to_ms_product_table_query= insert_for_table_with_product_id("oc_ms_product", rows_cursor_ms_product_table)

filter_list = table_struct_list_key(rows_cursor_filter_group_description_table, 'name') 
# makes a list of filters 

##################################################################################
##################################################################################
##################################################################################
#############------------------MAIN CALLABLE FUNCTION-------------------##########
##################################################################################
##################################################################################
##################################################################################

def multi_upload(main_dictionary_list, product_dictionary_dictlist):

	for i,v in enumerate(main_dictionary_list):

###### -FOR PRODUCT TABLE ONLY----------------############		
		product_dictionary_dictlist[i]['date_added'] = time.strftime('%Y-%m-%d %H:%M:%S')
		product_dictionary_dictlist[i]['model'] =  manufacturer_code +"_" + product_dictionary_dictlist[i]['sku']
		product_dictionary_dictlist[i]['stock_status_id'] = 7
		product_dictionary_dictlist[i]['weight_class_id'] = 1
		product_dictionary_dictlist[i]['subtract'] = 1
		product_dictionary_dictlist[i]['status'] = 1
		product_dictionary_dictlist[i]['shipping'] = 1
		product_dictionary_dictlist[i]['sort_order'] = 999
		product_dictionary_dictlist[i]['commission'] = COMM
		product_dictionary_dictlist[i]['tax_class_id'] = TAX_CLASS
		product_dictionary_dictlist[i]['seller_tax'] = SELL_TAX	


		if whether_img_folder :
		#######################################################################################
		######-----FOR IMAGE COLUMN in oc_product table WHEN IMAGES ARE IN SKU FOLDER---#######
		#######################################################################################

			image_path = local_image_root + re.sub('[^a-zA-Z0-9- \n\.]', '_' , str(main_dictionary_list[i]['sku']))
			# image_path = local_image_root + str(main_dictionary_list[i]['sku'])
			filenames = os.listdir(image_path)
			filenames_sorted = sorted(filenames)
			filenames_sorted = [x for x in filenames_sorted if x not in ['.DS_Store' ,'Thumbs.db']]
			print filenames_sorted

	 		first_image = filenames_sorted[0]
			product_dictionary_dictlist[i]['image'] = server_image_root+re.sub('[^a-zA-Z0-9- \n\.]', '_' , str(main_dictionary_list[i]['sku']))+"/"+str(first_image) 
			filenames_sorted.pop(0)	
			cursor_product_table.execute(upload_to_product_table_query, product_dictionary_dictlist[i])

			product_id = cursor_product_table.lastrowid	# FINDING THE ID OF LAST PRODUCT	
		
		else :

		############################################################################################
		#####--FOR IMAGE COLUMN in oc_product table WHEN IMAGES ARE """NOT""" IN SKU FOLDER--#######
		############################################################################################

			image_path_2 = local_image_root
			filenames_2 = os.listdir(image_path_2)
			# REPLACES ANY SPECIAL CHARACTERS IN SKUS WITH _ IN THE IMAGE FOLDER NAMES
			related_filenames = [names for names in filenames_2 if re.sub('[^a-zA-Z0-9- \n\.]', '_' , str(main_dictionary_list[i]['sku'])) in names]
			related_filenames_sorted = sorted(related_filenames)
			related_filenames_sorted = [x for x in related_filenames_sorted if x not in ['.DS_Store' ,'Thumbs.db']]
			print related_filenames_sorted

			first_image_2 = related_filenames_sorted[0]
			product_dictionary_dictlist[i]['image'] = server_image_root+str(first_image_2) 
			related_filenames_sorted.pop(0)	
			cursor_product_table.execute(upload_to_product_table_query, product_dictionary_dictlist[i])
			
			product_id = cursor_product_table.lastrowid	# FINDING THE ID OF LAST PRODUCT
		
###### -FOR PRODUCT FILTER TABLE ONLY----------------############		
		filter_uncommon_list = list(set(b)^set(filter_list))
		filter_common_list = b
		
		dict1 =[]
		for one, two in enumerate(rows_cursor_filter_group_description_table):
			for three, four in enumerate(filter_common_list):
				if filter_common_list[three] in two.values():
					for five,six in enumerate(rows_cursor_filter_description_table):
						if (product_list_variable[i][three].strip(" ") in set(six.values()))& ((two['filter_group_id'] in six.values())):
							dict1.append(six['filter_id'])
							t1 = { 'product_id' : product_id,
									'filter_id' : six['filter_id'] }
							cursor_filter_table.execute(sql_for_product_filter_table, t1) 
												
						if (product_list_variable[i][three].strip(" ") not in set(six.values()))& ((two['filter_group_id'] in six.values())):
							cursor_var_1.execute(sql_for_product_filter_description_table)
							new_rows = list(cursor_var_1.fetchall())
							variable_filter_list = table_struct_list_key(new_rows, 'name') 
							if product_list_variable[i][three] not in  variable_filter_list:
								if product_list_variable[i][three]:
									print "The filter {} is not in the filter list. Is this correct?".format(product_list_variable[i][three])
									answer = raw_input('Enter y/n :')
									if answer == 'y':
										sys.exit()

								# 	t4 = {'filter_group_id' : two['filter_group_id']}
								# 	generate_sql = """INSERT INTO oc_filter (filter_group_id) VALUES (%(filter_group_id)s)"""
								# 	cursor_oc_filter_table.execute(generate_sql, t4)
								# 	filter_id = cursor_oc_filter_table.lastrowid
								# 	print filter_id
								# 	generate_sql_1 = """INSERT INTO oc_filter_description (filter_id ,filter_group_id, name) VALUES (%(filter_id)s, %(filter_group_id)s, %(name)s)"""					
								# 	t5 =	{'filter_id' : filter_id ,
								# 			'filter_group_id' : two['filter_group_id'],
								# 			'name' : product_list_variable[i][three]} 
								# 	cursor_oc_filter_description_table.execute(generate_sql_1, t5)
								# 	t6 = { 'product_id' : product_id,
								# 			'filter_id' :  filter_id}
								# 	cursor_filter_table.execute(sql_for_product_filter_table, t6)				
							
				

###### -FOR PRODUCT DESCRIPTION TABLE ONLY----------------############		
		edited_rows_product_description_table_dictlist[i]['product_id'] = product_id 
		var = str(edited_rows_product_description_table_dictlist[i]['name'])
		edited_rows_product_description_table_dictlist[i]['meta_title'] = var + " @ Wholesale Prices"
		edited_rows_product_description_table_dictlist[i]['meta_description'] = var + " @ Wholesale Prices"
		edited_rows_product_description_table_dictlist[i]['tag'] = ",".join(var.split()) + " ethnicwear, " + "wholesale" 
		edited_rows_product_description_table_dictlist[i]['meta_keyword'] = ",".join(var.split()) + ", ethnicwear, " + "wholesale"
		edited_rows_product_description_table_dictlist[i]['language_id'] = 1

###### -FOR PRODUCT CATEGORY TABLE ONLY----------------############		
		edited_rows_product_category_table_dictlist[i]['product_id'] = product_id 
		edited_rows_product_category_table_dictlist[i]['category_id'] = CAT_ID 

###### -FOR MULTISELLER PRODUCT TABLE ONLY----------------############	
		query_1 = "SELECT seller_id FROM oc_ms_seller WHERE nickname = '"+ manufacturer_code +"'"  
		print query_1
		cursor_var_2.execute(query_1)
		new_rows_2 = list(cursor_var_2.fetchall())
		print new_rows_2
		edited_rows_ms_product_table_dictlist[i]['product_status'] =1
		edited_rows_ms_product_table_dictlist[i]['product_approved'] =1
		edited_rows_ms_product_table_dictlist[i]['product_id'] = product_id
		edited_rows_ms_product_table_dictlist[i]['seller_id'] = new_rows_2[0]['seller_id']

		if whether_img_folder :
	############################################################################################
	######-----FOR IMAGE COLUMN in oc_product_image table WHEN IMAGES ARE IN SKU FOLDER--#######
	############################################################################################

			for seven, eight in enumerate(filenames_sorted):
				t2 = {'product_id' : product_id,
					 	'image' : server_image_root +re.sub('[^a-zA-Z0-9- \n\.]', '_' , str(main_dictionary_list[i]['sku']))+"/"+ str(eight), # Attention 2 here for server path
					 	'sort_order' : seven + 2 }
				cursor_product_image_table.execute(sql_for_product_image_table, t2)
			
			t3 = {'product_id' : product_id, 'store_id' : 0}
		
		else :	
	###################################################################################################
	######--FOR IMAGE COLUMN in oc_product_image table WHEN IMAGES ARE """NOT""" IN SKU FOLDER--#######
	###################################################################################################

			for seven, eight in enumerate(related_filenames_sorted):
				t2 = {'product_id' : product_id,
					 	'image' : server_image_root + str(eight), 
					 	'sort_order' : seven + 2 }
				cursor_product_image_table.execute(sql_for_product_image_table, t2)
			
			t3 = {'product_id' : product_id, 'store_id' : 0}

###### -FOR URL-ALIAS TABLE ONLY---------------############	
	
		p_name = main_dictionary_list[i]['name'].lower()
		name_after_apostrophe = str.replace(p_name,"'","")
		name_after_ampersand = str.replace(name_after_apostrophe,"amp","");
		name_after_regex =  "-".join((re.sub('[^a-zA-Z0-9- \n]', '', name_after_ampersand)).split())
		model = product_dictionary_dictlist[i]['model'].lower()
		model_after_apostrophe = str.replace(model,"'","")
		model_after_ampersand = str.replace(model_after_apostrophe,"amp","");
		model_after_regex = "-".join((re.sub('[^a-zA-Z0-9- \n]', '', model_after_ampersand)).split())
		seo_line = name_after_regex + "-" + model_after_regex 


		table_query = "product_id=" + str(product_id)
		i_query = "INSERT INTO oc_url_alias SET query = '"+ table_query+"'" + ", keyword = '"+seo_line +"'"
		cursor_url_alias_table.execute(i_query)

		
###### -----------Query execution---------------############		
		cursor_product_description_table.execute(upload_to_product_description_table_query,edited_rows_product_description_table_dictlist[i])
		cursor_product_category_table.execute(upload_to_product_category_table_query,edited_rows_product_category_table_dictlist[i])
		cursor_product_to_store_table.execute(sql_for_product_to_store_table, t3)
		cursor_ms_product_table.execute(upload_to_ms_product_table_query,edited_rows_ms_product_table_dictlist[i])
		
######-- If Parent category-------#########
		t4 = {'product_id' : product_id, 'category_id' : Parent_cat}
		cursor_product_parent.execute(sql_for_product_to_category_table, t4)

		cnx.commit()
		
	return()	
						 
multi_upload(dictionary_from_CSV, edited_rows_product_table_dictlist)	

cnx.close()


