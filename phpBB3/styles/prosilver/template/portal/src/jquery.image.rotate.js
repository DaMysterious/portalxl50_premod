$(document).ready(function() {
						   
  var randomImages = new Array('header_00', 'header_01', 'header_02', 'header_03', 'header_04', 'header_05', 
							   'header_06', 'header_07', 'header_08', 'header_09', 'header_10', 'header_11',
							   'header_12', 'header_13', 'header_14', 'header_15', 'header_16', 'header_17',
							   'header_18', 'header_19', 'header_20', 'header_21', 'header_22', 'header_23',
							   'header_24', 'header_25', 'header_26', 'header_27', 'header_28', 'header_29',
							   'header_30', 'header_31', 'header_32', 'header_33', 'header_34', 'header_35',
							   'header_36', 'header_37', 'header_38', 'header_39', 'header_40', 'header_41',
							   'header_42', 'header_43', 'header_44', 'header_45', 'header_46', 'header_47',
							   'header_48', 'header_49', 'header_50', 'header_51', 'header_52', 'header_53',
							   'header_54', 'header_55', 'header_56', 'header_57', 'header_58', 'header_59',
							   'header_60', 'header_61', 'header_62', 'header_63', 'header_64', 'header_65',
							   'header_66', 'header_67', 'header_68', 'header_69', 'header_70', 'header_71',
							   'header_72', 'header_73', 'header_74', 'header_75', 'header_76', 'header_77',
							   'header_78', 'header_79', 'header_80', 'header_81', 'header_82', 'header_83', 
							   'header_84');
  
  var rndNum = Math.floor(Math.random() * randomImages.length);
	
  $(".headerbar").css({ background: "url({T_THEME_PATH}/headers/" + randomImages[rndNum] + ".jpg) no-repeat" });
  
});