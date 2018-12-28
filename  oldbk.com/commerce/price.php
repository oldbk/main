<?

$EKR_TO_GOLD = 20;
define("EKR_TO_KR",200); // курс екра к креду глобальный

{

$PRICE[1][cost]=25;
$PRICE[1][ekr]=true;
$PRICE[1][real]=true;
$PRICE[1][klan]=false;
$PRICE[1][desc]='Установка личного статичного подарка';
$PRICE[1][w]=60; //размеры 
$PRICE[1][h]=60; //размеры
$PRICE[1][param]='Название подарка'; 
$PRICE[1][anim]=false;
$PRICE[1][size]=20;
$PRICE[1][present]=true;
$PRICE[1][sert]=535;

$PRICE[2][cost]=50;
$PRICE[2][ekr]=true;
$PRICE[2][real]=true;
$PRICE[2][klan]=false;
$PRICE[2][desc]='Установка личного анимированного подарка';
$PRICE[2][w]=60; //размеры 
$PRICE[2][h]=60; //размеры
$PRICE[2][param]='Название подарка'; 
$PRICE[2][anim]=true;
$PRICE[2][size]=40;
$PRICE[2][present]=true;
$PRICE[2][sert]=535;

$PRICE[3][cost]=50;
$PRICE[3][ekr]=false;
$PRICE[3][real]=true;
$PRICE[3][klan]=true;
$PRICE[3][desc]='Установка кланового статичного подарка';
$PRICE[3][w]=60; //размеры 
$PRICE[3][h]=60; //размеры
$PRICE[3][param]='Название подарка'; 
$PRICE[3][anim]=false;
$PRICE[3][size]=20;
$PRICE[3][sert]=537;

$PRICE[4][cost]=100;
$PRICE[4][ekr]=false;
$PRICE[4][real]=true;
$PRICE[4][klan]=true;
$PRICE[4][desc]='Установка кланового анимированного подарка';
$PRICE[4][w]=60; //размеры 
$PRICE[4][h]=60; //размеры
$PRICE[4][param]='Название подарка'; 
$PRICE[4][anim]=true;
$PRICE[4][size]=40;
$PRICE[4][sert]=537;


$PRICE[5][cost]=50;
$PRICE[5][ekr]=true;
$PRICE[5][real]=true;
$PRICE[5][klan]=false;
$PRICE[5][desc]='Установка статичного личного образа';
$PRICE[5][w]=76; //размеры 
$PRICE[5][h]=209; //размеры
$PRICE[5][anim]=false;
$PRICE[5][size]=40;
$PRICE[5][size_prise_off]=2.5; //стоимость одного кб
$PRICE[5][present]=true;
$PRICE[5][sert]=542;

$PRICE[6][cost]=100;
$PRICE[6][ekr]=true;
$PRICE[6][real]=true;
$PRICE[6][klan]=false;
$PRICE[6][desc]='Установка анимированного личного образа';
$PRICE[6][w]=76; //размеры 
$PRICE[6][h]=209; //размеры
$PRICE[6][anim]=true;
$PRICE[6][size]=80;
$PRICE[6][size_prise_off]=2.5; //стоимость одного кб
$PRICE[6][present]=true;
$PRICE[6][sert]=542;

$PRICE[7][cost]=200;
$PRICE[7][ekr]=false;
$PRICE[7][real]=true;
$PRICE[7][klan]=true;
$PRICE[7][desc]='Установка одного (М или Ж) статичного кланового образа';
$PRICE[7][w]=76; //размеры 
$PRICE[7][h]=209; //размеры
$PRICE[7][anim]=false;
$PRICE[7][param]='Укажите для кого устанавливается образ (Мужской или Женский):<br><br>Выберите тип образа'; 
$PRICE[7][selectbox]='<select name="param"><option value="">---------------</option><option value="М">Мужской</option><option value="Ж">Женский</option></select><br><br>';
$PRICE[7][size]=40;
$PRICE[7][size_prise_off]=10; //стоимость одного кб
/*
$PRICE[8][cost]=300;
$PRICE[8][ekr]=true;
$PRICE[8][real]=true;
$PRICE[8][klan]=true;
$PRICE[8][desc]='Установка двух одинаковых для М и Ж статичных клановых образов';
$PRICE[8][w]=76; //размеры 
$PRICE[8][h]=209; //размеры
$PRICE[8][anim]=false;
$PRICE[8][size]=40;
$PRICE[8][size_prise_off]=15; //стоимость одного кб
*/

$PRICE[9][cost]=300;
$PRICE[9][ekr]=false;
$PRICE[9][real]=true;
$PRICE[9][klan]=true;
$PRICE[9][desc]='Установка одного анимированного (М или Ж) кланового образа';
$PRICE[9][w]=76; //размеры 
$PRICE[9][h]=209; //размеры
$PRICE[9][anim]=true;
$PRICE[9][param]='Укажите для кого устанавливается образ (Мужской или Женский):<br><br>Выберите тип образа';  
$PRICE[9][selectbox]='<select name="param"><option value="">---------------</option><option value="М">Мужской</option><option value="Ж">Женский</option></select><br><br>';
$PRICE[9][size]=80;
$PRICE[9][size_prise_off]=7.5; //стоимость одного кб

/*
$PRICE[10][cost]=450;
$PRICE[10][ekr]=true;
$PRICE[10][real]=true;
$PRICE[10][klan]=true;
$PRICE[10][desc]='Установка двух одинаковых для М и Ж анимированных клановых образов';
$PRICE[10][w]=76; //размеры 
$PRICE[10][h]=209; //размеры
$PRICE[10][anim]=true;
$PRICE[10][size]=80;
$PRICE[10][size_prise_off]=11.25; //стоимость одного кб
/////////////////////
*/

$PRICE[11][cost]=10;
$PRICE[11][ekr]=true;
$PRICE[11][real]=true;
$PRICE[11][desc]='Перезаливка личного образа из-за качества картинки';
$PRICE[11][w]=76; //размеры 
$PRICE[11][h]=209; //размеры
$PRICE[11][anim]=false;
$PRICE[11][param]='Выберите изображение для замены';


$PRICE[12][cost]=50;
$PRICE[12][ekr]=false;
$PRICE[12][real]=false;
$PRICE[12][desc]='Перезаливка кланового образа из-за качества картинки';
$PRICE[12][w]=76; //размеры 
$PRICE[12][h]=209; //размеры
$PRICE[12][anim]=false;
$PRICE[12][param]='Выберите изображение для замены';
$PRICE[12][klan]=true;

$PRICE[13][cost]=10;
$PRICE[13][ekr]=true;
$PRICE[13][real]=true;
$PRICE[13][desc]='Перезаливка личного подарка из-за качества картинки';
$PRICE[13][w]=60; //размеры 
$PRICE[13][h]=60; //размеры
$PRICE[13][anim]=false;
$PRICE[13][klan]=false;
$PRICE[13][param]='Выберите изображение для замены';

$PRICE[14][cost]=10;
$PRICE[14][ekr]=false;
$PRICE[14][real]=true;
$PRICE[14][desc]='Перезаливка кланового подарка из-за качества картинки';
$PRICE[14][w]=60; //размеры 
$PRICE[14][h]=60; //размеры
$PRICE[14][anim]=false;
$PRICE[14][klan]=false;
$PRICE[14][param]='Выберите изображение для замены';

$PRICE[15][cost]=7;
$PRICE[15][ekr]=true;
$PRICE[15][real]=true;
$PRICE[15][desc]='Перезаливка личного изображения вещи из-за качества картинки';
$PRICE[15][w]=60; //размеры 
$PRICE[15][h]=60; //размеры
$PRICE[15][anim]=false;
$PRICE[15][klan]=false;
$PRICE[15][param]='Выберите изображение для замены';

$PRICE[16][cost]=10;
$PRICE[16][ekr]=false;
$PRICE[16][real]=true;
$PRICE[16][desc]='Перезаливка кланового изображения вещи из-за качества картинки';
$PRICE[16][w]=60; //размеры 
$PRICE[16][h]=60; //размеры
$PRICE[16][anim]=false;
$PRICE[16][klan]=true;
$PRICE[16][param]='Выберите изображение для замены';
///////////

$PRICE[17][cost]=75;
$PRICE[17][ekr]=true;
$PRICE[17][real]=true;
$PRICE[17][desc]='Комплект из 11ти личных статичных изображений вещей';
$PRICE[17][w]=60; //размеры 
$PRICE[17][h]=60; //размеры
$PRICE[17][anim]=false;
$PRICE[17][klan]=false;
$PRICE[17][param]=array(21,22,23,24,25,26,27,28,29,30,31,31,31,32,33,34,35);
$PRICE[17][present]=true;

$PRICE[18][cost]=150;
$PRICE[18][ekr]=true;
$PRICE[18][real]=true;
$PRICE[18][desc]='Комплект из 11ти личных анимированых изображений вещей';
$PRICE[18][w]=60; //размеры 
$PRICE[18][h]=60; //размеры
$PRICE[18][anim]=true;
$PRICE[18][klan]=false;
$PRICE[18][param]=array(36,37,38,39,40,41,42,43,44,45,46,46,46,47,48,49,50);
$PRICE[18][present]=true;

$PRICE[19][cost]=300;
$PRICE[19][ekr]=true;
$PRICE[19][real]=true;
$PRICE[19][desc]='Комплект из 11ти клановых статичных изображений вещей';
$PRICE[19][w]=60; //размеры 
$PRICE[19][h]=60; //размеры
$PRICE[19][anim]=false;
$PRICE[19][klan]=true;
$PRICE[19][param]=array(51,52,53,54,55,56,57,58,59,60,61,61,61,62,63,64,65);

$PRICE[20][cost]=600;
$PRICE[20][ekr]=true;
$PRICE[20][real]=true;
$PRICE[20][desc]='Комплект из 11ти клановых анимированых изображений вещей';
$PRICE[20][w]=60; //размеры 
$PRICE[20][h]=60; //размеры
$PRICE[20][anim]=true;
$PRICE[20][klan]=true;
$PRICE[20][param]=array(66,67,68,69,70,71,72,73,74,75,76,76,76,77,78,79,80);



/////////Картинки личн.стат.
$PRICE[21][cost]=10;
$PRICE[21][ekr]=true;
$PRICE[21][real]=true;
$PRICE[21][klan]=false;
$PRICE[21][desc]='Личная статичная картинка на «Серьги»';
$PRICE[21][w]=60; //размеры 
$PRICE[21][h]=20; //размеры
$PRICE[21][anim]=false;
$PRICE[21][size]=20;
$PRICE[21][size_prise_off]=1; //стоимость одного кб
$PRICE[21][razdel]=4;
$PRICE[21][present]=true;
$PRICE[21][sert]=543;

$PRICE[22][cost]=10;
$PRICE[22][ekr]=true;
$PRICE[22][real]=true;
$PRICE[22][klan]=false;
$PRICE[22][desc]='Личная статичная картинка на «Кулон»';
$PRICE[22][w]=60; //размеры 
$PRICE[22][h]=20; //размеры
$PRICE[22][anim]=false;
$PRICE[22][size]=20;
$PRICE[22][size_prise_off]=1; //стоимость одного кб
$PRICE[22][razdel]=41;
$PRICE[22][present]=true;
$PRICE[22][sert]=543;

$PRICE[23][cost]=10;
$PRICE[23][ekr]=true;
$PRICE[23][real]=true;
$PRICE[23][klan]=false;
$PRICE[23][desc]='Личная статичная картинка на «Кастеты,ножи»';
$PRICE[23][w]=60; //размеры 
$PRICE[23][h]=60; //размеры
$PRICE[23][anim]=false;
$PRICE[23][size]=20;
$PRICE[23][size_prise_off]=1; //стоимость одного кб
$PRICE[23][razdel]=1;
$PRICE[23][present]=true;
$PRICE[23][sert]=543;

$PRICE[24][cost]=10;
$PRICE[24][ekr]=true;
$PRICE[24][real]=true;
$PRICE[24][klan]=false;
$PRICE[24][desc]='Личная статичная картинка на «Топоры»';
$PRICE[24][w]=60; //размеры 
$PRICE[24][h]=60; //размеры
$PRICE[24][anim]=false;
$PRICE[24][size]=20;
$PRICE[24][size_prise_off]=1; //стоимость одного кб
$PRICE[24][razdel]=11;
$PRICE[24][present]=true;
$PRICE[24][sert]=543;

$PRICE[25][cost]=10;
$PRICE[25][ekr]=true;
$PRICE[25][real]=true;
$PRICE[25][klan]=false;
$PRICE[25][desc]='Личная статичная картинка на «Дубины,булавы,молоты»';
$PRICE[25][w]=60; //размеры 
$PRICE[25][h]=60; //размеры
$PRICE[25][anim]=false;
$PRICE[25][size]=20;
$PRICE[25][size_prise_off]=1; //стоимость одного кб
$PRICE[25][razdel]=12;
$PRICE[25][present]=true;
$PRICE[25][sert]=543;

$PRICE[26][cost]=10;
$PRICE[26][ekr]=true;
$PRICE[26][real]=true;
$PRICE[26][klan]=false;
$PRICE[26][desc]='Личная статичная картинка на «Мечи»';
$PRICE[26][w]=60; //размеры 
$PRICE[26][h]=60; //размеры
$PRICE[26][anim]=false;
$PRICE[26][size]=20;
$PRICE[26][size_prise_off]=1; //стоимость одного кб
$PRICE[26][razdel]=13;
$PRICE[26][present]=true;
$PRICE[26][sert]=543;

$PRICE[27][cost]=10;
$PRICE[27][ekr]=true;
$PRICE[27][real]=true;
$PRICE[27][klan]=false;
$PRICE[27][desc]='Личная статичная картинка на «Луки и арбалеты»';
$PRICE[27][w]=60; //размеры 
$PRICE[27][h]=60; //размеры
$PRICE[27][anim]=false;
$PRICE[27][size]=20;
$PRICE[27][size_prise_off]=1; //стоимость одного кб
$PRICE[27][razdel]=14;
$PRICE[27][present]=true;
$PRICE[27][sert]=543;

$PRICE[28][cost]=10;
$PRICE[28][ekr]=true;
$PRICE[28][real]=true;
$PRICE[28][klan]=false;
$PRICE[28][desc]='Личная статичная картинка на «Легкую броню»';
$PRICE[28][w]=60; //размеры 
$PRICE[28][h]=80; //размеры
$PRICE[28][anim]=false;
$PRICE[28][size]=20;
$PRICE[28][size_prise_off]=1; //стоимость одного кб
$PRICE[28][razdel]=22;
$PRICE[28][present]=true;
$PRICE[28][sert]=543;

$PRICE[29][cost]=10;
$PRICE[29][ekr]=true;
$PRICE[29][real]=true;
$PRICE[29][klan]=false;
$PRICE[29][desc]='Личная статичная картинка на «Тяжелую броню»';
$PRICE[29][w]=60; //размеры 
$PRICE[29][h]=80; //размеры
$PRICE[29][anim]=false;
$PRICE[29][size]=20;
$PRICE[29][size_prise_off]=1; //стоимость одного кб
$PRICE[29][razdel]=23;
$PRICE[29][present]=true;
$PRICE[29][sert]=543;

$PRICE[30][cost]=10;
$PRICE[30][ekr]=true;
$PRICE[30][real]=true;
$PRICE[30][klan]=false;
$PRICE[30][desc]='Личная статичная картинка на «Плащ»';
$PRICE[30][w]=60; //размеры 
$PRICE[30][h]=80; //размеры
$PRICE[30][anim]=false;
$PRICE[30][size]=20;
$PRICE[30][size_prise_off]=1; //стоимость одного кб
$PRICE[30][razdel]=6;
$PRICE[30][present]=true;
$PRICE[30][sert]=543;

$PRICE[31][cost]=3;
$PRICE[31][ekr]=true;
$PRICE[31][real]=true;
$PRICE[31][klan]=false;
$PRICE[31][desc]='Личная статичная картинка на «Кольца»';
$PRICE[31][w]=20; //размеры 
$PRICE[31][h]=20; //размеры
$PRICE[31][anim]=false;
$PRICE[31][size_prise_off]=1; //стоимость одного кб
$PRICE[31][size]=20;
$PRICE[31][razdel]=42;
$PRICE[31][present]=true;
$PRICE[31][sert]=543;

$PRICE[32][cost]=10;
$PRICE[32][ekr]=true;
$PRICE[32][real]=true;
$PRICE[32][klan]=false;
$PRICE[32][desc]='Личная статичная картинка на «Шлем»';
$PRICE[32][w]=60; //размеры 
$PRICE[32][h]=60; //размеры
$PRICE[32][anim]=false;
$PRICE[32][size_prise_off]=1; //стоимость одного кб
$PRICE[32][size]=20;
$PRICE[32][razdel]=24;
$PRICE[32][present]=true;
$PRICE[32][sert]=543;

$PRICE[33][cost]=10;
$PRICE[33][ekr]=true;
$PRICE[33][real]=true;
$PRICE[33][klan]=false;
$PRICE[33][desc]='Личная статичная картинка на «Перчатки»';
$PRICE[33][w]=60; //размеры 
$PRICE[33][h]=40; //размеры
$PRICE[33][anim]=false;
$PRICE[33][size]=20;
$PRICE[33][size_prise_off]=1; //стоимость одного кб
$PRICE[33][razdel]=21;
$PRICE[33][present]=true;
$PRICE[33][sert]=543;

$PRICE[34][cost]=10;
$PRICE[34][ekr]=true;
$PRICE[34][real]=true;
$PRICE[34][klan]=false;
$PRICE[34][desc]='Личная статичная картинка на «Щиты»';
$PRICE[34][w]=60; //размеры 
$PRICE[34][h]=60; //размеры
$PRICE[34][anim]=false;
$PRICE[34][size]=20;
$PRICE[34][size_prise_off]=1; //стоимость одного кб
$PRICE[34][razdel]=3;
$PRICE[34][present]=true;
$PRICE[34][sert]=543;

$PRICE[35][cost]=10;
$PRICE[35][ekr]=true;
$PRICE[35][real]=true;
$PRICE[35][klan]=false;
$PRICE[35][desc]='Личная статичная картинка на «Сапоги»';
$PRICE[35][w]=60; //размеры 
$PRICE[35][h]=40; //размеры
$PRICE[35][anim]=false;
$PRICE[35][size_prise_off]=1; //стоимость одного кб
$PRICE[35][size]=20;
$PRICE[35][razdel]=2;
$PRICE[35][present]=true;
$PRICE[35][sert]=543;

//////лич. аним.
$PRICE[36][cost]=20;
$PRICE[36][ekr]=true;
$PRICE[36][real]=true;
$PRICE[36][klan]=false;
$PRICE[36][desc]='Личная анимированная картинка на «Серьги»';
$PRICE[36][w]=60; //размеры 
$PRICE[36][h]=20; //размеры
$PRICE[36][anim]=true;
$PRICE[36][size]=40;
$PRICE[36][size_prise_off]=1; //стоимость одного кб
$PRICE[36][razdel]=4;
$PRICE[36][present]=true;
$PRICE[36][sert]=543;

$PRICE[37][cost]=20;
$PRICE[37][ekr]=true;
$PRICE[37][real]=true;
$PRICE[37][klan]=false;
$PRICE[37][desc]='Личная анимированная картинка на «Кулон»';
$PRICE[37][w]=60; //размеры 
$PRICE[37][h]=20; //размеры
$PRICE[37][anim]=true;
$PRICE[37][size]=40;
$PRICE[37][size_prise_off]=1; //стоимость одного кб
$PRICE[37][razdel]=41;
$PRICE[37][present]=true;
$PRICE[37][sert]=543;

$PRICE[38][cost]=20;
$PRICE[38][ekr]=true;
$PRICE[38][real]=true;
$PRICE[38][klan]=false;
$PRICE[38][desc]='Личная анимированная картинка на «Кастеты,ножи»';
$PRICE[38][w]=60; //размеры 
$PRICE[38][h]=60; //размеры
$PRICE[38][anim]=true;
$PRICE[38][size]=40;
$PRICE[38][size_prise_off]=1; //стоимость одного кб
$PRICE[38][razdel]=1;
$PRICE[38][present]=true;
$PRICE[38][sert]=543;

$PRICE[39][cost]=20;
$PRICE[39][ekr]=true;
$PRICE[39][real]=true;
$PRICE[39][klan]=false;
$PRICE[39][desc]='Личная анимированная картинка на «Топоры»';
$PRICE[39][w]=60; //размеры 
$PRICE[39][h]=60; //размеры
$PRICE[39][anim]=true;
$PRICE[39][size]=40;
$PRICE[39][size_prise_off]=1; //стоимость одного кб
$PRICE[39][razdel]=11;
$PRICE[39][present]=true;
$PRICE[39][sert]=543;

$PRICE[40][cost]=20;
$PRICE[40][ekr]=true;
$PRICE[40][real]=true;
$PRICE[40][klan]=false;
$PRICE[40][desc]='Личная анимированная картинка на «Дубины,булавы,молоты»';
$PRICE[40][w]=60; //размеры 
$PRICE[40][h]=60; //размеры
$PRICE[40][anim]=true;
$PRICE[40][size]=40;
$PRICE[40][size_prise_off]=1; //стоимость одного кб
$PRICE[40][razdel]=12;
$PRICE[40][present]=true;
$PRICE[40][sert]=543;

$PRICE[41][cost]=20;
$PRICE[41][ekr]=true;
$PRICE[41][real]=true;
$PRICE[41][klan]=false;
$PRICE[41][desc]='Личная анимированная картинка на «Мечи»';
$PRICE[41][w]=60; //размеры 
$PRICE[41][h]=60; //размеры
$PRICE[41][anim]=true;
$PRICE[41][size]=40;
$PRICE[41][size_prise_off]=1; //стоимость одного кб
$PRICE[41][razdel]=13;
$PRICE[41][present]=true;
$PRICE[41][sert]=543;

$PRICE[42][cost]=20;
$PRICE[42][ekr]=true;
$PRICE[42][real]=true;
$PRICE[42][klan]=false;
$PRICE[42][desc]='Личная анимированная картинка на «Луки и арбалеты»';
$PRICE[42][w]=60; //размеры 
$PRICE[42][h]=60; //размеры
$PRICE[42][anim]=true;
$PRICE[42][size]=40;
$PRICE[42][size_prise_off]=1; //стоимость одного кб
$PRICE[42][razdel]=14;
$PRICE[42][present]=true;
$PRICE[42][sert]=543;

$PRICE[43][cost]=20;
$PRICE[43][ekr]=true;
$PRICE[43][real]=true;
$PRICE[43][klan]=false;
$PRICE[43][desc]='Личная анимированная картинка на «Легкую броню»';
$PRICE[43][w]=60; //размеры 
$PRICE[43][h]=80; //размеры
$PRICE[43][anim]=true;
$PRICE[43][size]=40;
$PRICE[43][size_prise_off]=1; //стоимость одного кб
$PRICE[43][razdel]=22;
$PRICE[43][present]=true;
$PRICE[43][sert]=543;

$PRICE[44][cost]=20;
$PRICE[44][ekr]=true;
$PRICE[44][real]=true;
$PRICE[44][klan]=false;
$PRICE[44][desc]='Личная анимированная картинка на «Тяжелую броню»';
$PRICE[44][w]=60; //размеры 
$PRICE[44][h]=80; //размеры
$PRICE[44][anim]=true;
$PRICE[44][size]=40;
$PRICE[44][size_prise_off]=1; //стоимость одного кб
$PRICE[44][razdel]=23;
$PRICE[44][present]=true;
$PRICE[44][sert]=543;

$PRICE[45][cost]=20;
$PRICE[45][ekr]=true;
$PRICE[45][real]=true;
$PRICE[45][klan]=false;
$PRICE[45][desc]='Личная анимированная картинка на «Плащ»';
$PRICE[45][w]=60; //размеры 
$PRICE[45][h]=80; //размеры
$PRICE[45][anim]=true;
$PRICE[45][size]=40;
$PRICE[45][size_prise_off]=1; //стоимость одного кб
$PRICE[45][razdel]=6;
$PRICE[45][present]=true;
$PRICE[45][sert]=543;

$PRICE[46][cost]=7;
$PRICE[46][ekr]=true;
$PRICE[46][real]=true;
$PRICE[46][klan]=false;
$PRICE[46][desc]='Личная анимированная картинка на «Кольца»';
$PRICE[46][w]=20; //размеры 
$PRICE[46][h]=20; //размеры
$PRICE[46][anim]=true;
$PRICE[46][size]=40;
$PRICE[46][size_prise_off]=0.35; //стоимость одного кб
$PRICE[46][razdel]=42;
$PRICE[46][present]=true;
$PRICE[46][sert]=543;

$PRICE[47][cost]=20;
$PRICE[47][ekr]=true;
$PRICE[47][real]=true;
$PRICE[47][klan]=false;
$PRICE[47][desc]='Личная анимированная картинка на «Шлем»';
$PRICE[47][w]=60; //размеры 
$PRICE[47][h]=60; //размеры
$PRICE[47][anim]=true;
$PRICE[47][size]=40;
$PRICE[47][size_prise_off]=1; //стоимость одного кб
$PRICE[47][razdel]=24;
$PRICE[47][present]=true;
$PRICE[47][sert]=543;

$PRICE[48][cost]=20;
$PRICE[48][ekr]=true;
$PRICE[48][real]=true;
$PRICE[48][klan]=false;
$PRICE[48][desc]='Личная анимированная картинка на «Перчатки»';
$PRICE[48][w]=60; //размеры 
$PRICE[48][h]=40; //размеры
$PRICE[48][anim]=true;
$PRICE[48][size]=40;
$PRICE[48][size_prise_off]=1; //стоимость одного кб
$PRICE[48][razdel]=21;
$PRICE[48][present]=true;
$PRICE[48][sert]=543;

$PRICE[49][cost]=20;
$PRICE[49][ekr]=true;
$PRICE[49][real]=true;
$PRICE[49][klan]=false;
$PRICE[49][desc]='Личная анимированная картинка на «Щиты»';
$PRICE[49][w]=60; //размеры 
$PRICE[49][h]=60; //размеры
$PRICE[49][anim]=true;
$PRICE[49][size]=40;
$PRICE[49][size_prise_off]=1; //стоимость одного кб
$PRICE[49][razdel]=3;
$PRICE[49][present]=true;
$PRICE[49][sert]=543;

$PRICE[50][cost]=20;
$PRICE[50][ekr]=true;
$PRICE[50][real]=true;
$PRICE[50][klan]=false;
$PRICE[50][desc]='Личная анимированная картинка на «Сапоги»';
$PRICE[50][w]=60; //размеры 
$PRICE[50][h]=40; //размеры
$PRICE[50][anim]=true;
$PRICE[50][size]=40;
$PRICE[50][size_prise_off]=1; //стоимость одного кб
$PRICE[50][razdel]=2;
$PRICE[50][present]=true;
$PRICE[50][sert]=543;
//Клан.стат.
$PRICE[51][cost]=40;
$PRICE[51][ekr]=true;
$PRICE[51][real]=true;
$PRICE[51][klan]=true;
$PRICE[51][desc]='Клановая статичная картинка на «Серьги»';
$PRICE[51][w]=60; //размеры 
$PRICE[51][h]=20; //размеры
$PRICE[51][anim]=false;
$PRICE[51][size]=20;
$PRICE[51][size_prise_off]=4; //стоимость одного кб
$PRICE[51][razdel]=4;

$PRICE[52][cost]=40;
$PRICE[52][ekr]=true;
$PRICE[52][real]=true;
$PRICE[52][klan]=true;
$PRICE[52][desc]='Клановая статичная картинка на «Кулон»';
$PRICE[52][w]=60; //размеры 
$PRICE[52][h]=20; //размеры
$PRICE[52][anim]=false;
$PRICE[52][size]=20;
$PRICE[52][size_prise_off]=4; //стоимость одного кб
$PRICE[52][razdel]=41;

$PRICE[53][cost]=40;
$PRICE[53][ekr]=true;
$PRICE[53][real]=true;
$PRICE[53][klan]=true;
$PRICE[53][desc]='Клановая статичная картинка на «Кастеты,ножи»';
$PRICE[53][w]=60; //размеры 
$PRICE[53][h]=60; //размеры
$PRICE[53][anim]=false;
$PRICE[53][size]=20;
$PRICE[53][size_prise_off]=4; //стоимость одного кб
$PRICE[53][razdel]=1;

$PRICE[54][cost]=40;
$PRICE[54][ekr]=true;
$PRICE[54][real]=true;
$PRICE[54][klan]=true;
$PRICE[54][desc]='Клановая статичная картинка на «Топоры»';
$PRICE[54][w]=60; //размеры 
$PRICE[54][h]=60; //размеры
$PRICE[54][anim]=false;
$PRICE[54][size]=20;
$PRICE[54][size_prise_off]=4; //стоимость одного кб
$PRICE[54][razdel]=11;

$PRICE[55][cost]=40;
$PRICE[55][ekr]=true;
$PRICE[55][real]=true;
$PRICE[55][klan]=true;
$PRICE[55][desc]='Клановая статичная картинка на «Дубины,булавы,молоты»';
$PRICE[55][w]=60; //размеры 
$PRICE[55][h]=60; //размеры
$PRICE[55][anim]=false;
$PRICE[55][size]=20;
$PRICE[55][size_prise_off]=4; //стоимость одного кб
$PRICE[55][razdel]=12;

$PRICE[56][cost]=40;
$PRICE[56][ekr]=true;
$PRICE[56][real]=true;
$PRICE[56][klan]=true;
$PRICE[56][desc]='Клановая статичная картинка на «Мечи»';
$PRICE[56][w]=60; //размеры 
$PRICE[56][h]=60; //размеры
$PRICE[56][anim]=false;
$PRICE[56][size]=20;
$PRICE[56][size_prise_off]=4; //стоимость одного кб
$PRICE[56][razdel]=13;

$PRICE[57][cost]=40;
$PRICE[57][ekr]=true;
$PRICE[57][real]=true;
$PRICE[57][klan]=true;
$PRICE[57][desc]='Клановая статичная картинка на «Луки и арбалеты»';
$PRICE[57][w]=60; //размеры 
$PRICE[57][h]=60; //размеры
$PRICE[57][anim]=false;
$PRICE[57][size]=20;
$PRICE[57][size_prise_off]=4; //стоимость одного кб
$PRICE[57][razdel]=14;

$PRICE[58][cost]=40;
$PRICE[58][ekr]=true;
$PRICE[58][real]=true;
$PRICE[58][klan]=true;
$PRICE[58][desc]='Клановая статичная картинка на «Легкую бронь»';
$PRICE[58][w]=60; //размеры 
$PRICE[58][h]=80; //размеры
$PRICE[58][anim]=false;
$PRICE[58][size]=20;
$PRICE[58][size_prise_off]=4; //стоимость одного кб
$PRICE[58][razdel]=22;

$PRICE[59][cost]=40;
$PRICE[59][ekr]=true;
$PRICE[59][real]=true;
$PRICE[59][klan]=true;
$PRICE[59][desc]='Клановая статичная картинка на «Тяжелую бронь»';
$PRICE[59][w]=60; //размеры 
$PRICE[59][h]=80; //размеры
$PRICE[59][anim]=false;
$PRICE[59][size]=20;
$PRICE[59][size_prise_off]=4; //стоимость одного кб
$PRICE[59][razdel]=23;

$PRICE[60][cost]=40;
$PRICE[60][ekr]=true;
$PRICE[60][real]=true;
$PRICE[60][klan]=true;
$PRICE[60][desc]='Клановая статичная картинка на «Плащ»';
$PRICE[60][w]=60; //размеры 
$PRICE[60][h]=80; //размеры
$PRICE[60][anim]=false;
$PRICE[60][size]=20;
$PRICE[60][size_prise_off]=4; //стоимость одного кб
$PRICE[60][razdel]=6;

$PRICE[61][cost]=13;
$PRICE[61][ekr]=true;
$PRICE[61][real]=true;
$PRICE[61][klan]=true;
$PRICE[61][desc]='Клановая статичная картинка на «Кольца»';
$PRICE[61][w]=20; //размеры 
$PRICE[61][h]=20; //размеры
$PRICE[61][anim]=false;
$PRICE[61][size]=20;
$PRICE[61][size_prise_off]=1.35; //стоимость одного кб
$PRICE[61][razdel]=42;

$PRICE[62][cost]=40;
$PRICE[62][ekr]=true;
$PRICE[62][real]=true;
$PRICE[62][klan]=true;
$PRICE[62][desc]='Клановая статичная картинка на «Шлем»';
$PRICE[62][w]=60; //размеры 
$PRICE[62][h]=60; //размеры
$PRICE[62][anim]=false;
$PRICE[62][size]=20;
$PRICE[62][size_prise_off]=4; //стоимость одного кб
$PRICE[62][razdel]=24;

$PRICE[63][cost]=40;
$PRICE[63][ekr]=true;
$PRICE[63][real]=true;
$PRICE[63][klan]=true;
$PRICE[63][desc]='Клановая статичная картинка на «Перчатки»';
$PRICE[63][w]=60; //размеры 
$PRICE[63][h]=40; //размеры
$PRICE[63][anim]=false;
$PRICE[63][size]=20;
$PRICE[63][size_prise_off]=4; //стоимость одного кб
$PRICE[63][razdel]=21;

$PRICE[64][cost]=40;
$PRICE[64][ekr]=true;
$PRICE[64][real]=true;
$PRICE[64][klan]=true;
$PRICE[64][desc]='Клановая статичная картинка на «Щиты»';
$PRICE[64][w]=60; //размеры 
$PRICE[64][h]=60; //размеры
$PRICE[64][anim]=false;
$PRICE[64][size]=20;
$PRICE[64][size_prise_off]=4; //стоимость одного кб
$PRICE[64][razdel]=3;

$PRICE[65][cost]=40;
$PRICE[65][ekr]=true;
$PRICE[65][real]=true;
$PRICE[65][klan]=true;
$PRICE[65][desc]='Клановая статичная картинка на «Сапоги»';
$PRICE[65][w]=60; //размеры 
$PRICE[65][h]=40; //размеры
$PRICE[65][anim]=false;
$PRICE[65][size]=20;
$PRICE[65][size_prise_off]=4; //стоимость одного кб
$PRICE[65][razdel]=2;
//////
//Клан аним
$PRICE[66][cost]=80;
$PRICE[66][ekr]=true;
$PRICE[66][real]=true;
$PRICE[66][klan]=true;
$PRICE[66][desc]='Клановая анимированная картинка на «Серьги»';
$PRICE[66][w]=60; //размеры 
$PRICE[66][h]=20; //размеры
$PRICE[66][anim]=true;
$PRICE[66][size]=40;
$PRICE[66][size_prise_off]=4; //стоимость одного кб
$PRICE[66][razdel]=4;

$PRICE[67][cost]=80;
$PRICE[67][ekr]=true;
$PRICE[67][real]=true;
$PRICE[67][klan]=true;
$PRICE[67][desc]='Клановая анимированная картинка на «Кулон»';
$PRICE[67][w]=60; //размеры 
$PRICE[67][h]=20; //размеры
$PRICE[67][anim]=true;
$PRICE[67][size]=40;
$PRICE[67][size_prise_off]=4; //стоимость одного кб
$PRICE[67][razdel]=41;

$PRICE[68][cost]=80;
$PRICE[68][ekr]=true;
$PRICE[68][real]=true;
$PRICE[68][klan]=true;
$PRICE[68][desc]='Клановая анимированная картинка на «Кастеты,ножи»';
$PRICE[68][w]=60; //размеры 
$PRICE[68][h]=60; //размеры
$PRICE[68][anim]=true;
$PRICE[68][size]=40;
$PRICE[68][size_prise_off]=4; //стоимость одного кб
$PRICE[68][razdel]=1;

$PRICE[69][cost]=80;
$PRICE[69][ekr]=true;
$PRICE[69][real]=true;
$PRICE[69][klan]=true;
$PRICE[69][desc]='Клановая анимированная картинка на «Топоры»';
$PRICE[69][w]=60; //размеры 
$PRICE[69][h]=60; //размеры
$PRICE[69][anim]=true;
$PRICE[69][size]=40;
$PRICE[69][size_prise_off]=4; //стоимость одного кб
$PRICE[69][razdel]=11;

$PRICE[70][cost]=80;
$PRICE[70][ekr]=true;
$PRICE[70][real]=true;
$PRICE[70][klan]=true;
$PRICE[70][desc]='Клановая анимированная картинка на «Дубины,булавы,молоты»';
$PRICE[70][w]=60; //размеры 
$PRICE[70][h]=60; //размеры
$PRICE[70][anim]=true;
$PRICE[70][size]=40;
$PRICE[70][size_prise_off]=4; //стоимость одного кб
$PRICE[70][razdel]=12;

$PRICE[71][cost]=80;
$PRICE[71][ekr]=true;
$PRICE[71][real]=true;
$PRICE[71][klan]=true;
$PRICE[71][desc]='Клановая анимированная картинка на «Мечи»';
$PRICE[71][w]=60; //размеры 
$PRICE[71][h]=60; //размеры
$PRICE[71][anim]=true;
$PRICE[71][size]=40;
$PRICE[71][size_prise_off]=4; //стоимость одного кб
$PRICE[71][razdel]=13;

$PRICE[72][cost]=80;
$PRICE[72][ekr]=true;
$PRICE[72][real]=true;
$PRICE[72][klan]=true;
$PRICE[72][desc]='Клановая анимированная картинка на «Луки и арбалеты»';
$PRICE[72][w]=60; //размеры 
$PRICE[72][h]=60; //размеры
$PRICE[72][anim]=true;
$PRICE[72][size]=40;
$PRICE[72][size_prise_off]=4; //стоимость одного кб
$PRICE[72][razdel]=14;

$PRICE[73][cost]=80;
$PRICE[73][ekr]=true;
$PRICE[73][real]=true;
$PRICE[73][klan]=true;
$PRICE[73][desc]='Клановая анимированная картинка на «Легкую броню»';
$PRICE[73][w]=60; //размеры 
$PRICE[73][h]=80; //размеры
$PRICE[73][anim]=true;
$PRICE[73][size]=40;
$PRICE[73][size_prise_off]=4; //стоимость одного кб
$PRICE[73][razdel]=22;

$PRICE[74][cost]=80;
$PRICE[74][ekr]=true;
$PRICE[74][real]=true;
$PRICE[74][klan]=true;
$PRICE[74][desc]='Клановая анимированная картинка на «Тяжелую броню»';
$PRICE[74][w]=60; //размеры 
$PRICE[74][h]=80; //размеры
$PRICE[74][anim]=true;
$PRICE[74][size]=40;
$PRICE[74][size_prise_off]=4; //стоимость одного кб
$PRICE[74][razdel]=23;

$PRICE[75][cost]=80;
$PRICE[75][ekr]=true;
$PRICE[75][real]=true;
$PRICE[75][klan]=true;
$PRICE[75][desc]='Клановая анимированная картинка на «Плащ»';
$PRICE[75][w]=60; //размеры 
$PRICE[75][h]=80; //размеры
$PRICE[75][anim]=true;
$PRICE[75][size]=40;
$PRICE[75][size_prise_off]=4; //стоимость одного кб
$PRICE[75][razdel]=6;

$PRICE[76][cost]=27;
$PRICE[76][ekr]=true;
$PRICE[76][real]=true;
$PRICE[76][klan]=true;
$PRICE[76][desc]='Клановая анимированная картинка на «Кольца»';
$PRICE[76][w]=20; //размеры 
$PRICE[76][h]=20; //размеры
$PRICE[76][anim]=true;
$PRICE[76][size]=40;
$PRICE[76][size_prise_off]=1.35; //стоимость одного кб
$PRICE[76][razdel]=42;

$PRICE[77][cost]=80;
$PRICE[77][ekr]=true;
$PRICE[77][real]=true;
$PRICE[77][klan]=true;
$PRICE[77][desc]='Клановая анимированная картинка на «Шлем»';
$PRICE[77][w]=60; //размеры 
$PRICE[77][h]=60; //размеры
$PRICE[77][anim]=true;
$PRICE[77][size]=40;
$PRICE[77][size_prise_off]=4; //стоимость одного кб
$PRICE[77][razdel]=24;

$PRICE[78][cost]=80;
$PRICE[78][ekr]=true;
$PRICE[78][real]=true;
$PRICE[78][klan]=true;
$PRICE[78][desc]='Клановая анимированная картинка на «Перчатки»';
$PRICE[78][w]=60; //размеры 
$PRICE[78][h]=40; //размеры
$PRICE[78][anim]=true;
$PRICE[78][size]=40;
$PRICE[78][size_prise_off]=4; //стоимость одного кб
$PRICE[78][razdel]=21;

$PRICE[79][cost]=80;
$PRICE[79][ekr]=true;
$PRICE[79][real]=true;
$PRICE[79][klan]=true;
$PRICE[79][desc]='Клановая анимированная картинка на «Щиты»';
$PRICE[79][w]=60; //размеры 
$PRICE[79][h]=60; //размеры
$PRICE[79][anim]=true;
$PRICE[79][size]=40;
$PRICE[79][size_prise_off]=4; //стоимость одного кб
$PRICE[79][razdel]=3;

$PRICE[80][cost]=80;
$PRICE[80][ekr]=true;
$PRICE[80][real]=true;
$PRICE[80][klan]=true;
$PRICE[80][desc]='Клановая анимированная картинка на «Сапоги»';
$PRICE[80][w]=60; //размеры 
$PRICE[80][h]=40; //размеры
$PRICE[80][anim]=true;
$PRICE[80][size]=40;
$PRICE[80][size_prise_off]=4; //стоимость одного кб
$PRICE[80][razdel]=2;
////
$PRICE[81][cost]=300;
$PRICE[81][ekr]=true;
$PRICE[81][real]=true;
$PRICE[81][klan]=true;
$PRICE[81][desc]='Установка двух (М и Ж) статичных клановых образов';
$PRICE[81][w]=76; //размеры 
$PRICE[81][h]=209; //размеры
$PRICE[81][anim]=false;
$PRICE[81][size]=40;
$PRICE[81][size_prise_off]=15; //стоимость одного кб
$PRICE[81][param]=array(7,7);

$PRICE[82][cost]=450;
$PRICE[82][ekr]=true;
$PRICE[82][real]=true;
$PRICE[82][klan]=true;
$PRICE[82][desc]='Установка двух (М и Ж) анимированных клановых образов';
$PRICE[82][w]=76; //размеры 
$PRICE[82][h]=209; //размеры
$PRICE[82][anim]=true;
$PRICE[82][size]=80;
$PRICE[82][size_prise_off]=11.25; //стоимость одного кб
$PRICE[82][param]=array(9,9);


$PRICE[83][cost]=50;
$PRICE[83][ekr]=true;
$PRICE[83][real]=true;
$PRICE[83][desc]='Замена статичного образа на анимированый';
$PRICE[83][w]=76; //размеры 
$PRICE[83][h]=209; //размеры
$PRICE[83][anim]=true;
$PRICE[83][param]='Выберите изображение';



$PRICE[90][cost]=50;
$PRICE[90][real]=true;
$PRICE[90][vau]=false;
$PRICE[90][ekr]=true;
$PRICE[90][desc]='Установка личного смайла';
$PRICE[90][w]=15; //размеры 
$PRICE[90][h]=15; //размеры
$PRICE[90][wmax]=60; //размеры 
$PRICE[90][hmax]=30; //размеры
$PRICE[90][anim]=true;
$PRICE[90][size]=20;
$PRICE[90][present]=true;
$PRICE[90][sert]=533;
#$PRICE[90][param]='Выберите изображение';


$PRICE[91][cost]=200;
$PRICE[91][real]=true;
$PRICE[91][klan]=true;
$PRICE[91][ekr]=false;
$PRICE[91][desc]='Установка кланового смайла';
$PRICE[91][w]=15; //размеры 
$PRICE[91][h]=15; //размеры
$PRICE[91][wmax]=60; //размеры 
$PRICE[91][hmax]=30; //размеры
$PRICE[91][anim]=true;
$PRICE[91][size]=40;
$PRICE[91][sert]=534;
#$PRICE[91][param]='Выберите изображение';




//настройки доп
$PRICE[100][cost]=5;
$PRICE[100][real]=true;
$PRICE[100][klan]=true;
$PRICE[100][desc]='Смена пароля на казну клана';

if(time()>mktime(17,0,0,11,29,2016) && time()<mktime(23,59,59,12,6,2016)) {
	$PRICE[101][cost]=0;
} else {
	$PRICE[101][cost]=30;
}
$PRICE[101][real]=true;
$PRICE[101][klan]=true;
$PRICE[101][desc]='Смена склонности клана и соклановцам';

$PRICE[102][cost]=25;
$PRICE[102][real]=true;
$PRICE[102][klan]=true;
$PRICE[102][desc]='Смена значка клана';

$PRICE[103][cost]=50;
$PRICE[103][real]=true;
$PRICE[103][klan]=true;
$PRICE[103][desc]='Смена названия клана';

$PRICE[104][cost]=10;
$PRICE[104][real]=true;
$PRICE[104][vau]=true;
$PRICE[104][klan]=false;
$PRICE[104][desc]='Смена главы клана';

$PRICE[105][cost]=50;
$PRICE[105][real]=true;
$PRICE[105][klan]=true;
$PRICE[105][desc]='Присоединить рекрут-клан';

$PRICE[106][cost]=50;
$PRICE[106][real]=true;
$PRICE[106][klan]=true;
$PRICE[106][desc]='Отсоединить рекрут-клан';

/*
$PRICE[201][cost]=25;
$PRICE[201][ekr]=false;
$PRICE[201][real]=true;
$PRICE[201][klan]=false;
$PRICE[201][desc]='Смена даты рождения персонажа';
*/

/*
$PRICE[202][cost]=15;
$PRICE[202][ekr]=true;
$PRICE[202][real]=true;
$PRICE[202][klan]=false;
$PRICE[202][desc]='Смена пароля персонажа';
*/

$PRICE[203][cost]=15;
$PRICE[203][real]=true;
$PRICE[203][klan]=false;
$PRICE[203][desc]='Сброс второго пароля персонажа';

/*
$PRICE[204][cost]=15;
$PRICE[204][ekr]=true;
$PRICE[204][real]=true;
$PRICE[204][klan]=false;
$PRICE[204][desc]='Смена пароля на банковский счёт';
*/

$PRICE[204][cost]=15;
$PRICE[204][real]=true;
$PRICE[204][klan]=false;
$PRICE[204][desc]='Смена пола персонажа';

$PRICE[205][cost]=10;
$PRICE[205][real]=true;
$PRICE[205][klan]=false;
$PRICE[205][desc]='Возврат вещи, в случае, если вещь продана в магазин или выкинута по ошибке';

$PRICE[206][cost]=15;
$PRICE[206][real]=true;
$PRICE[206][klan]=false;
$PRICE[206][desc]='Снятие подарка с вещи, в случае, если вещь подарена по ошибке';

$PRICE[207][cost]=15;
$PRICE[207][real]=true;
$PRICE[207][klan]=false;
$PRICE[207][desc]='Экспресс проверка на чистоту';

/*
$PRICE[208][cost]=20;
$PRICE[208][real]=true;
$PRICE[208][klan]=false;
$PRICE[208][desc]='Обмен одной обычной вещи персонажу';


$PRICE[209][cost]=40;
$PRICE[209][real]=true;
$PRICE[209][klan]=false;
$PRICE[209][desc]='Обмен одной уникальной вещи персонажу';
*/

$PRICE[210][cost]=100;
$PRICE[210][real]=true;
$PRICE[210][klan]=false;
$PRICE[210][desc]='Обмен уникальной вещи 7-10 уровней на уникальную вещь 11 уровня';

/*
$PRICE[211][cost]=200;
$PRICE[211][real]=true;
$PRICE[211][klan]=false;
$PRICE[211][desc]='Покупка уникальной вещи (топ мф +3 стата)';
*/

/*
$PRICE[213][cost]=40;
$PRICE[213][real]=true;
$PRICE[213][klan]=false;
$PRICE[213][desc]='Обмен не привязанного артефакта';
*/

$PRICE[214][cost]=15;
$PRICE[214][real]=true;
$PRICE[214][klan]=false;
$PRICE[214][desc]='Смена емейла персонажа';

$PRICE[215][cost]=50;
$PRICE[215][real]=true;
$PRICE[215][klan]=false;
$PRICE[215][vau]=true;
$PRICE[215][desc]='Восстановление клана после расформирования (при неуплате налога)';

/*
$PRICE[221][cost]=20;
$PRICE[221][vau]=true;
$PRICE[221][real]=true;
$PRICE[221][klan]=false;
$PRICE[221][desc]='Сброс модификаторов и статов для рун';
*/
/*
$PRICE[222][cost]=25;
$PRICE[222][vau]=false;
$PRICE[222][real]=true;
$PRICE[222][klan]=false;
$PRICE[222][desc]='Смена WMZ кошелька для вывода денежных средств';
*/
/*
$PRICE[223][cost]=20;
$PRICE[223][vau]=true;
$PRICE[223][real]=true;
$PRICE[223][klan]=false;
$PRICE[223][desc]='Сброс статов для уникальных Плащей героя';
*/

/*
Стоимость обмена Плаща рыцаря на Плащ Героя (или Плащ героя на Плащ легендарного рыцаря)
     5 екр + 4000 кр для обычного плаща
     40 екр. для уникального плаща
     45 екр. для улучшенного уникального плаща
*/

$PRICE[224][cost]=5;
$PRICE[224][cost_add_kr]=4000; //добавка в кр
$PRICE[224][cost_unik_ekr]=40; // екры за уу
$PRICE[224][vau]=true;
$PRICE[224][real]=true;
$PRICE[224][klan]=false;
$PRICE[224][desc]='Обмен Плащей рыцаря (мф) на Плащ героя (мф)';

$PRICE[225][cost]=0;
$PRICE[225][vau]=false;
$PRICE[225][ekr]=true;
$PRICE[225][real]=false;
$PRICE[225][klan]=false;
$PRICE[225][desc]='Cменить пароль от не основного счета на такой же как основной';

///до 224 занято раздел другое

$PRICE[226][cost]=5;
$PRICE[226][cost_add_kr]=4000; //добавка в кр
$PRICE[226][cost_unik_ekr]=40; // екры за уу
$PRICE[226][vau]=true;
$PRICE[226][real]=true;
$PRICE[226][klan]=false;
$PRICE[226][desc]='Обмен Футболки ОлдБК (мф) на Футболку Учителей (мф)';


$PRICE[227][cost]=0;
$PRICE[227][real]=true;
$PRICE[227][desc]='Оплата Штрафа';


$PRICE[228][cost]=5;
$PRICE[228][real]=true;
$PRICE[228][desc]='Лечение неизлечимой травмы';
 
 
 /*
Стоимость обмена Плаща рыцаря на Плащ Героя (или Плащ героя на Плащ легендарного рыцаря)
     5 екр + 4000 кр для обычного плаща
     40 екр. для уникального плаща
     45 екр. для улучшенного уникального плаща
*/
 
$PRICE[229][cost]=5; //екр
$PRICE[229][cost_add_kr]=4000; //добавка в кр
$PRICE[229][cost_unik_ekr]=40; // екры за уу
$PRICE[229][vau]=true;
$PRICE[229][real]=true;
$PRICE[229][klan]=false;
$PRICE[229][desc]='Обмен Плаща героя (мф) на Плащ легендарного рыцаря (мф)';

/*
Стоимость обмена Плаща рыцаря на Плащ легендарного рыцаря
     10 екр + 8000 кр для обычного плаща
     80 екр. для уникального плаща
     90 екр. для улучшенного уникального плаща
*/

$PRICE[230][cost]=10;
$PRICE[230][cost_add_kr]=8000; //добавка в кр
$PRICE[230][cost_unik_ekr]=80; // екры за уу
$PRICE[230][vau]=true;
$PRICE[230][real]=true;
$PRICE[230][klan]=false;
$PRICE[230][desc]='Обмен Плаща рыцаря (мф) на Плащ легендарного рыцаря (мф)';
  

$PRICE[231][cost]=5;
$PRICE[231][cost_add_kr]=4000;
$PRICE[231][cost_unik_ekr]=40;
$PRICE[231][vau]=true;
$PRICE[231][real]=true;
$PRICE[231][klan]=false;
$PRICE[231][desc]='Обмен Футболки Учителей (мф) на Легендарную футболку ОлдБК (мф)';


$PRICE[232][cost]=10;
$PRICE[232][cost_add_kr]=8000;
$PRICE[232][cost_unik_ekr]=80;
$PRICE[232][vau]=true;
$PRICE[232][real]=true;
$PRICE[232][klan]=false;
$PRICE[232][desc]='Обмен Футболки ОлдБК (мф) на Легендарную футболку ОлдБК (мф)';


$PRICE[233][cost]=5;
$PRICE[233][cost_add_kr]=4000;
$PRICE[233][cost_unik_gold]=600; //добавка в золоте у или уу
//$PRICE[233][cost_unik_ekr]=40;
$PRICE[233][vau]=true;
$PRICE[233][real]=true;
$PRICE[233][klan]=false;
$PRICE[233][desc]='Обмен Легендарной футболки ОлдБК (мф) на Легендарную футболку Учителей (мф)';

$PRICE[234][cost]=10;
$PRICE[234][cost_add_kr]=8000;
$PRICE[234][cost_unik_gold]=1000; //добавка в золоте у или уу
//$PRICE[234][cost_unik_ekr]=80;
$PRICE[234][vau]=true;
$PRICE[234][real]=true;
$PRICE[234][klan]=false;
$PRICE[234][desc]='Обмен Футболки Учителей (мф) на Легендарную футболку Учителей (мф)';

$PRICE[235][cost]=15;
$PRICE[235][cost_add_kr]=12000;
$PRICE[235][cost_unik_gold]=1600; //добавка в золоте у или уу
//$PRICE[235][cost_unik_ekr]=120;
$PRICE[235][vau]=true;
$PRICE[235][real]=true;
$PRICE[235][klan]=false;
$PRICE[235][desc]='Обмен Футболки ОлдБК (мф) на Легендарную футболку Учителей (мф)';





$PRICE[241][cost]=15; // екр
$PRICE[241][cost_add_kr]=12000; //добавка в кр
$PRICE[241][cost_unik_gold]=1600; //добавка в золоте у или уу
//$PRICE[241][cost_unik_ekr]=120; // екры за уу
$PRICE[241][vau]=true;
$PRICE[241][real]=true;
$PRICE[241][klan]=false;
$PRICE[241][desc]='Обмен Плаща рыцаря (мф) на Плащ легендарного героя (мф)';

$PRICE[242][cost]=10; //екр
$PRICE[242][cost_add_kr]=8000; //добавка в кр
$PRICE[242][cost_unik_gold]=1000; //добавка в золоте у или уу
//$PRICE[242][cost_unik_ekr]=80; // екры за уу
$PRICE[242][vau]=true;
$PRICE[242][real]=true;
$PRICE[242][klan]=false;
$PRICE[242][desc]='Обмен Плаща героя (мф) на Плащ легендарного героя (мф)';

$PRICE[243][cost]=5;
$PRICE[243][cost_add_kr]=4000; //добавка в кр
$PRICE[243][cost_unik_gold]=600; //добавка в золоте у или уу
//$PRICE[243][cost_unik_ekr]=40; // екры за уу
$PRICE[243][vau]=true;
$PRICE[243][real]=true;
$PRICE[243][klan]=false;
$PRICE[243][desc]='Обмен Плаща легендарного рыцаря (мф) на Плащ легендарного героя (мф)';


 
 
 

//клан-абилки- только общая настройка
$PRICE[300][cost]=0;
$PRICE[300][vau]=false;
$PRICE[300][ekr]=true;
$PRICE[300][klan]=true;
$PRICE[300][desc]='Клановые Реликты';

//личные абилки

$PRICE[301][cost]=15;
$PRICE[301][kol]=5;
$PRICE[301][magid]=10101;
$PRICE[301][vau]=false;
$PRICE[301][ekr]=true;
$PRICE[301][real]=false;
$PRICE[301][desc]='Личный Реликт «Невидимость»';


$PRICE[302][cost]=3;
$PRICE[302][kol]=50;
$PRICE[302][magid]=14;
$PRICE[302][vau]=false;
$PRICE[302][ekr]=true;
$PRICE[302][real]=false;
$PRICE[302][desc]='Личный Реликт «Заклятие молчания 15 мин»';

$PRICE[303][cost]=5;
$PRICE[303][kol]=25;
$PRICE[303][magid]=15;
$PRICE[303][vau]=false;
$PRICE[303][ekr]=true;
$PRICE[303][real]=false;
$PRICE[303][desc]='Личный Реликт «Заклятие молчания 30 мин»';

$PRICE[304][cost]=35;
$PRICE[304][kol]=5;
$PRICE[304][magid]=49;
$PRICE[304][vau]=false;
$PRICE[304][ekr]=true;
$PRICE[304][real]=false;
$PRICE[304][desc]='Личный Реликт «Выход из боя»';


/*
$PRICE[305][cost]=4;
$PRICE[305][kol]=50;
$PRICE[305][magid]=5151;
$PRICE[305][vau]=false;
$PRICE[305][ekr]=true;
$PRICE[305][real]=false;
$PRICE[305][desc]='Личный Реликт «Записки коментатора»';
*/

$PRICE[306][cost]=12;
$PRICE[306][kol]=5;
$PRICE[306][magid]=53;
$PRICE[306][vau]=false;
$PRICE[306][ekr]=true;
$PRICE[306][real]=false;
$PRICE[306][desc]='Личный Реликт «Заступиться»';


$PRICE[307][cost]=3;
$PRICE[307][kol]=50;
$PRICE[307][magid]=54;
$PRICE[307][vau]=false;
$PRICE[307][ekr]=true;
$PRICE[307][real]=false;
$PRICE[307][desc]='Личный Реликт «Колодец здоровья»';

$PRICE[308][cost]=6;
$PRICE[308][kol]=30;
$PRICE[308][magid]=55;
$PRICE[308][vau]=false;
$PRICE[308][ekr]=true;
$PRICE[308][real]=false;
$PRICE[308][desc]='Личный Реликт «Нападение»';

$PRICE[309][cost]=10;
$PRICE[309][kol]=25;
$PRICE[309][magid]=56;
$PRICE[309][vau]=false;
$PRICE[309][ekr]=true;
$PRICE[309][real]=false;
$PRICE[309][desc]='Личный Реликт «Кровавое нападение»';


$PRICE[310][cost]=5; //7
$PRICE[310][kol]=20;
$PRICE[310][magid]=57;
$PRICE[310][vau]=false;
$PRICE[310][ekr]=true;
$PRICE[310][real]=false;
$PRICE[310][desc]='Личный Реликт «Лечение травм»';


$PRICE[311][cost]=6;
$PRICE[311][kol]=5;
$PRICE[311][magid]=82;
$PRICE[311][vau]=false;
$PRICE[311][ekr]=true;
$PRICE[311][real]=false;
$PRICE[311][desc]='Личный Реликт «Карта Лабиринта»';

$PRICE[312][cost]=10;
$PRICE[312][kol]=10;
$PRICE[312][magid]=2525;
$PRICE[312][vau]=false;
$PRICE[312][ekr]=true;
$PRICE[312][real]=false;
$PRICE[312][desc]='Личный Реликт «Вендетта»';

/*
$PRICE[313][cost]=20;
$PRICE[313][kol]=5;
$PRICE[313][magid]=10000;
$PRICE[313][vau]=true;
$PRICE[313][real]=false;
$PRICE[313][desc]='Личный Реликт «Телепорт»';
*/

$PRICE[313][cost]=16;
$PRICE[313][kol]=15;
$PRICE[313][magid]=5017152;
$PRICE[313][vau]=true;
$PRICE[313][real]=false;
$PRICE[313][desc]='Личный Реликт «Огненный ожог» (Гнев Ареса, 360 мин)';

//сделать код для выдачи хилокл
$PRICE[314][cost]=24;
$PRICE[314][kol]=30;
$PRICE[314][magid]=273;
$PRICE[314][eshopid]=200273;
$PRICE[314][vau]=true;
$PRICE[314][real]=false;
$PRICE[314][desc]='Свиток «Восстановление энергии 360HP»';

$PRICE[315][cost]=16;
$PRICE[315][kol]=15;
$PRICE[315][magid]=5017153;
$PRICE[315][vau]=true;
$PRICE[315][real]=false;
$PRICE[315][desc]='Личный Реликт «Потрясение» (Вой Грифона, 360 мин)';

$PRICE[316][cost]=16;
$PRICE[316][kol]=15;
$PRICE[316][magid]=5017154;
$PRICE[316][vau]=true;
$PRICE[316][real]=false;
$PRICE[316][desc]='Личный Реликт «Подлый удар» - (Обман Химеры, 360 мин)';

$PRICE[317][cost]=16;
$PRICE[317][kol]=15;
$PRICE[317][magid]=5017155;
$PRICE[317][vau]=true;
$PRICE[317][real]=false;
$PRICE[317][desc]='Личный Реликт «Отравление ядом» (Укус Гидры, 360 мин)';



$PRICE[444][cost]=0;
$PRICE[444][kol]=1;
$PRICE[444][real]=true;
$PRICE[444][desc]='Оплата досрочного выхода из темницы';


$PRICE[500][cost]=20;
$PRICE[500][ekr]=true;
//$PRICE[500][vau]=true;
$PRICE[500][real]=true;
$PRICE[500][desc]='Статичная картинка колец для одного из супругов';
$PRICE[500][w]=30; //размеры 
$PRICE[500][h]=30; //размеры
$PRICE[500][wmax]=60; //размеры 
$PRICE[500][hmax]=30; //размеры
$PRICE[500][size]=5;
$PRICE[500][razdel]=99;
$PRICE[500][param]='Укажите для кого устанавливается образ колец (Муж или Жена):<br><br>Выберите персонажа'; 
$PRICE[500][selectbox]='<select name="param"><option value="">---------------</option><option value="М">Муж</option><option value="Ж">Жена</option></select><br><br>';

$PRICE[501][cost]=40;
//$PRICE[501][vau]=true;
$PRICE[501][ekr]=true;
$PRICE[501][real]=true;
$PRICE[501][desc]='Статичная картинка колец для обоих супругов';
$PRICE[501][w]=30; //размеры 
$PRICE[501][h]=30; //размеры
$PRICE[501][wmax]=60; //размеры 
$PRICE[501][hmax]=30; //размеры
$PRICE[501][size]=5;
$PRICE[501][razdel]=99;


$PRICE[502][cost]=40;
//$PRICE[502][vau]=true;
$PRICE[502][ekr]=true;
$PRICE[502][anim]=true;
$PRICE[502][real]=true;
$PRICE[502][desc]='Анимированная картинка колец для одного из супругов';
$PRICE[502][w]=30; //размеры 
$PRICE[502][h]=30; //размеры
$PRICE[502][wmax]=60; //размеры 
$PRICE[502][hmax]=30; //размеры
$PRICE[502][size]=10;
$PRICE[502][razdel]=99;
$PRICE[502][param]='Укажите для кого устанавливается образ кольца (Муж или Жена):<br><br>Выберите тип образа'; 
$PRICE[502][selectbox]='<select name="param"><option value="">---------------</option><option value="М">Муж</option><option value="Ж">Жена</option></select><br><br>';



$PRICE[503][cost]=80;
//$PRICE[503][vau]=true;
$PRICE[503][ekr]=true;
$PRICE[503][real]=true;
$PRICE[503][anim]=true;
$PRICE[503][desc]='Анимированная картинка колец для обоих супругов';
$PRICE[503][w]=30; //размеры 
$PRICE[503][h]=30; //размеры
$PRICE[503][wmax]=60; //размеры 
$PRICE[503][hmax]=30; //размеры
$PRICE[503][size]=10;
$PRICE[503][razdel]=99;


$PRICE[600][cost]=10;
$PRICE[600][ekr]=true;
$PRICE[600][real]=true;
$PRICE[600][desc]='Замена статичной картинки (все кроме «Колец») на анимированную';
$PRICE[600][w]=60; //размеры 
$PRICE[600][h]=60; //размеры
$PRICE[600][anim]=true;
$PRICE[600][klan]=false;
$PRICE[600][param]='Выберите изображение для замены';

$PRICE[601][cost]=4;
$PRICE[601][ekr]=true;
$PRICE[601][real]=true;
$PRICE[601][desc]='Замена статичной картинки «Кольца» на анимированную';
$PRICE[601][w]=60; //размеры 
$PRICE[601][h]=60; //размеры
$PRICE[601][anim]=true;
$PRICE[601][klan]=false;
$PRICE[601][param]='Выберите изображение для замены';

$PRICE[602][cost]=40;
$PRICE[602][ekr]=true;
$PRICE[602][real]=true;
$PRICE[602][desc]='Замена статичной клановой картинки (все кроме «Колец») на анимированную';
$PRICE[602][w]=60; //размеры 
$PRICE[602][h]=60; //размеры
$PRICE[602][anim]=true;
$PRICE[602][klan]=true;
$PRICE[602][param]='Выберите изображение для замены';

$PRICE[603][cost]=14;
$PRICE[603][ekr]=true;
$PRICE[603][real]=true;
$PRICE[603][desc]='Замена статичной клановой картинки «Кольца» на анимированную';
$PRICE[603][w]=60; //размеры 
$PRICE[603][h]=60; //размеры
$PRICE[603][anim]=true;
$PRICE[603][klan]=true;
$PRICE[603][param]='Выберите изображение для замены';

$PRICE[612][cost]=100;
$PRICE[612][ekr]=false;
$PRICE[612][real]=false;
$PRICE[612][desc]='Перезаливка кланового образа со статичного на анимированный';
$PRICE[612][w]=76; //размеры 
$PRICE[612][h]=209; //размеры
$PRICE[612][anim]=true;
$PRICE[612][param]='Выберите изображение для замены';
$PRICE[612][klan]=true;

/*
$PRICE[613][cost]=10;
$PRICE[613][ekr]=true;
$PRICE[613][real]=true;
$PRICE[613][desc]='Замена картинки артефакта на другую статичную';
$PRICE[613][w]=60; //размеры 
$PRICE[613][h]=60; //размеры
$PRICE[613][anim]=true;
$PRICE[613][klan]=false;
$PRICE[613][param]='Выберите изображение для замены';

$PRICE[614][cost]=20;
$PRICE[614][ekr]=true;
$PRICE[614][real]=true;
$PRICE[614][desc]='Замена картинки артефакта на другую анимированную';
$PRICE[614][w]=60; //размеры 
$PRICE[614][h]=60; //размеры
$PRICE[614][anim]=true;
$PRICE[614][klan]=false;
$PRICE[614][param]='Выберите изображение для замены';
*/

function print_price()
{
global $PRICE;

?>
<table>
<tr> <td valign="top" width=50% ><h1>Склонности и образы:<br /><font size=-2>(возможен заказ и оплата через дилеров или списание со счета)</font></h1></td>
<td valign="top" style="padding-top:4px;">Описание услуги и её стоимость:</td></tr>

<tr><td><b>Уникальный подарок: </b></td><td></td></tr>

<? if ($PRICE[1][desc]) { 
    ?>

<tr><td valign="top"><a class="button" href="?act=service&menu=1"><? echo $PRICE[1][desc];?></a></td>
<td valign="top" style="padding-top:4px;"><b><? echo get_cost($PRICE[1]);?></b></td>
</tr>
<?
}

if ($PRICE[2][desc])
      { 	
?>

<tr><td valign="top"><a class="button" href="?act=service&menu=2"><? echo $PRICE[2][desc];?></a></td>
<td valign="top" style="padding-top:4px;"><b><? echo get_cost($PRICE[2]);?></b></td>
</tr>
<?
	}
if ($PRICE[3][desc])
      { 	
?>
<tr><td valign="top"><a class="button" href="?act=service&menu=3"><? echo $PRICE[3][desc];?></a></td>
<td valign="top" style="padding-top:4px;"><b><? echo get_cost($PRICE[3]);?></b></td></tr>
<?
	}
if ($PRICE[4][desc])
      { 	
?>
<tr><td valign="top"><a class="button" href="?act=service&menu=4"><? echo $PRICE[4][desc];?></a></td>
<td valign="top" style="padding-top:4px;"><b><? echo get_cost($PRICE[4]);?></b></td></tr>
<?
	}
if ($PRICE[13][desc])
      { 	
?>
<tr><td valign="top"><a class="button" href="?act=service&menu=13"><? echo $PRICE[13][desc];?></a></td>
<td valign="top" style="padding-top:4px;"><b><? echo get_cost($PRICE[13]);?></b><br>(смена или перезаливка из-за качества)</td></tr>
<?
	}
if ($PRICE[14][desc])
      { 	
?>
<tr><td valign="top"><a class="button" href="?act=service&menu=14"><? echo $PRICE[14][desc];?></a></td>
<td valign="top" style="padding-top:4px;"><b><? echo get_cost($PRICE[14]);?></b><br>(смена или перезаливка из-за качества)</td></tr>
<?
	}
if ($PRICE[5][desc])
      { 	
?>
<tr><td><b>Образы: </b></td><td></td></tr>
<tr><td valign="top"><a class="button" href="?act=service&menu=5"><? echo $PRICE[5][desc];?></a></td>
<td valign="top" style="padding-top:4px;"><b><? echo get_cost($PRICE[5]);?></b></td></tr>
<?
	}
if ($PRICE[6][desc])
      { 	
?>
<tr><td valign="top"><a class="button" href="?act=service&menu=6"><? echo $PRICE[6][desc];?></a></td>
<td valign="top" style="padding-top:4px;"><b><? echo get_cost($PRICE[6]);?></b></td></tr>
<?
	}
if ($PRICE[7][desc])
      { 	
?>
<tr><td valign="top"><a class="button" href="?act=service&menu=7"><? echo $PRICE[7][desc];?></a></td>
<td valign="top" style="padding-top:4px;">(мужской либо женский)  <b><? echo get_cost($PRICE[7]);?></b></td></tr>
<?
	}
if ($PRICE[8][desc])
      { 	
?>
<tr><td valign="top"><a class="button" href="?act=service&menu=8"><? echo $PRICE[8][desc];?></a></td>
<td valign="top" style="padding-top:4px;">(<b>одинаковые</b> мужской и женский) <b><? echo get_cost($PRICE[8]);?></b></td></tr>
<?
	}
if ($PRICE[81][desc])
      { 	
?>
<tr><td valign="top"><a class="button" href="?act=service&menu=81"><? echo $PRICE[81][desc];?></a></td>
<td valign="top" style="padding-top:4px;">(<b>разные</b> мужской и женский) <b><? echo get_cost($PRICE[81]);?></b></td></tr>
<?
	}	
if ($PRICE[9][desc])
      { 	
?>
<tr><td valign="top"><a class="button" href="?act=service&menu=9"><? echo $PRICE[9][desc];?></a></td>
<td valign="top" style="padding-top:4px;">(мужской либо женский) <b><? echo get_cost($PRICE[9]);?></b></td></tr>
<?
	}
if ($PRICE[10][desc])
      { 	
?>
<tr><td valign="top"><a class="button" href="?act=service&menu=10"><? echo $PRICE[10][desc];?></a></td>
<td valign="top" style="padding-top:4px;">(<b>одинаковые</b> мужской и женский) <b><? echo get_cost($PRICE[10]);?></b></td></tr>
<?
	}
if ($PRICE[82][desc])
      { 	
?>
<tr><td valign="top"><a class="button" href="?act=service&menu=82"><? echo $PRICE[82][desc];?></a></td>
<td valign="top" style="padding-top:4px;">(<b>разные</b> мужской и женский) <b><? echo get_cost($PRICE[82]);?></b></td></tr>
<?
	}	
if ($PRICE[11][desc])
      { 	
?>
<tr><td valign="top"><br><a class="button" href="?act=service&menu=11"><? echo $PRICE[11][desc];?></a></td>
<td valign="top" style="padding-top:4px;"><br><b><? echo get_cost($PRICE[11]);?></b><br>(смена или перезаливка из-за качества)</td></tr>
<?
	}	
if ($PRICE[83][desc])
      { 	
?>
<tr><td valign="top"><br><a class="button" href="?act=service&menu=83"><? echo $PRICE[83][desc];?></a></td>
<td valign="top" style="padding-top:4px;"><br><b><? echo get_cost($PRICE[83]);?></b><br>(смена со статичного на анимированный)</td></tr>
<?
	}
if ($PRICE[12][desc])
      { 	
?>
<tr><td valign="top"><a class="button" href="?act=service&menu=12"><? echo $PRICE[12][desc];?></a></td>
<td valign="top" style="padding-top:4px;"><b><? echo get_cost($PRICE[12]);?></b><br>(смена или перезаливка из-за качества)</td></tr>
<?
	}
?>

</table>
<div class="item" style="padding-top:1px;"></div>
<table>
<tr><td valign="top"><h1>Изображения вещей:<br /><font size=-2>(возможен заказ и оплата через дилеров или списание со счета)</font></h1></td>
<td valign="top" style="padding-top:4px;">Описание услуги и её стоимость:</td></tr>

<tr><td><b>Личные статичные картинки: </b></td><td></td></tr>
<?
for ($ii=21;$ii<=35;$ii++)
{
if ($PRICE[$ii][desc])
      { 	
	echo '<tr><td valign="top"><a class="button" href="?act=service&menu='.$ii.'">';
	echo $PRICE[$ii][desc];
	echo '</a></td><td valign="top" style="padding-top:4px;"><b>';
	echo get_cost($PRICE[$ii]);
	echo '</b><br></td></tr>';
	}
 }
 
if ($PRICE[17][desc])
      { 	
?>
<tr><td valign="center"><br><a class="button" href="?act=service&menu=17"><? echo $PRICE[17][desc];?></a></td>
<td valign="center" style="padding-top:4px;"><br><b><? echo get_cost($PRICE[17]);?></b><br></td></tr>
<?
	}
 
?>
<tr><td><b>Личные анимированные картинки: </b></td><td></td></tr>
<?
for ($ii=36;$ii<=50;$ii++)
{
if ($PRICE[$ii][desc])
      { 	
	echo '<tr><td valign="top"><a class="button" href="?act=service&menu='.$ii.'">';
	echo $PRICE[$ii][desc];
	echo '</a></td><td valign="top" style="padding-top:4px;"><b>';
	echo get_cost($PRICE[$ii]);
	echo '</b><br></td></tr>';
	}
 }
 
 if ($PRICE[18][desc])
      { 	
?>
<tr><td valign="center"><br><a class="button" href="?act=service&menu=18"><? echo $PRICE[18][desc];?></a></td>
<td valign="center" style="padding-top:4px;"><br><b><? echo get_cost($PRICE[18]);?></b><br></td></tr>
<?
	}
 
 if ($PRICE[15][desc])
      { 	
?>
<tr><td valign="top"><br><a class="button" href="?act=service&menu=15"><? echo $PRICE[15][desc];?></a></td>
<td valign="top" style="padding-top:4px;"><br><b><? echo get_cost($PRICE[15]);?></b><br>(смена или перезаливка из-за качества)</td></tr>
<?
	}
 
  if ($PRICE[51][desc])
  {
?>
<tr><td><b>Клановые статичные картинки: </b></td><td></td></tr>
<?
 }
for ($ii=51;$ii<=65;$ii++)
{
if ($PRICE[$ii][desc])
      { 	
	echo '<tr><td valign="top"><a class="button" href="?act=service&menu='.$ii.'">';
	echo $PRICE[$ii][desc];
	echo '</a></td><td valign="top" style="padding-top:4px;"><b>';
	echo get_cost($PRICE[$ii]);
	echo '</b><br></td></tr>';
	}
 }

if ($PRICE[19][desc])
      { 	
?>
<tr><td valign="center"><br><a class="button" href="?act=service&menu=19"><? echo $PRICE[19][desc];?></a></td>
<td valign="center" style="padding-top:4px;"><br><b><? echo get_cost($PRICE[19]);?></b><br></td></tr>
<?
	}

  if ($PRICE[66][desc])
  {
?>
<tr><td><b>Клановые анимированные картинки: </b></td><td></td></tr>
<?
 }
for ($ii=66;$ii<=80;$ii++)
{
if ($PRICE[$ii][desc])
      { 	
	echo '<tr><td valign="top"><a class="button" href="?act=service&menu='.$ii.'">';
	echo $PRICE[$ii][desc];
	echo '</a></td><td valign="top" style="padding-top:4px;"><b>';
	echo get_cost($PRICE[$ii]);
	echo '</b><br></td></tr>';
	}
 }
 
if ($PRICE[20][desc])
      { 	
?>
<tr><td valign="center"><br><a class="button" href="?act=service&menu=20"><? echo $PRICE[20][desc];?></a></td>
<td valign="center" style="padding-top:4px;"><br><b><? echo get_cost($PRICE[20]);?></b><br></td></tr>
<?
	} 
 
  if ($PRICE[16][desc])
      { 	
?>
<tr><td valign="top"><br><a class="button" href="?act=service&menu=16"><? echo $PRICE[16][desc];?></a></td>
<td valign="top" style="padding-top:4px;"><br><b><? echo get_cost($PRICE[16]);?></b><br>(смена или перезаливка из-за качества)</td></tr>
<?
	}
?>
</table>

<div class="item" style="padding-top:1px;"></div>
<table width=100%>

<tr><td valign="top"><h1>Клановые услуги:</h1></td>
<td valign="top" style="padding-top:1px;">Описание услуги и её стоимость:</td>
</tr>

<tr><td valign="top">
<input type="checkbox" name="services[]" value="Клан: смена склонности клану">Клан: смена склонности клану
</td><td valign="top" style="padding-top:4px;">30 долларов</td></tr><tr><td valign="top">
<input type="checkbox" name="services[]" value="Клан: смена значка клана">Клан: смена значка клана
</td><td valign="top" style="padding-top:4px;">25 долларов</td></tr><tr><td valign="top">
<input type="checkbox" name="services[]" value="Клан: смена названия клана">Клан: смена названия клана
</td><td valign="top" style="padding-top:4px;">150 долларов</td></tr><tr><td valign="top">
<input type="checkbox" name="services[]" value="Клан: смена главы клана">Клан: смена главы клана
</td><td valign="top" style="padding-top:4px;">200 кредитов</td></tr><tr><td valign="top">
</table>

<div class="item" style="padding-top:1px;"></div>
<table width=75%>
<tr><td valign="top"><h1>Услуги коммерческого отдела:</h1></td><td valign="top" style="padding-top:4px;">Описание услуги и её стоимость:</td></tr>
<tr><td valign="top">
<input type="checkbox" name="services[]" value="Ком. отдел: смена даты рождения персонажа">Смена даты рождения персонажа
</td><td valign="top" style="padding-top:4px;">25 долларов/екр</td></tr><tr><td valign="top">
<input type="checkbox" name="services[]" value="Ком. отдел: смена пароля">Смена пароля
</td><td valign="top" style="padding-top:4px;">15 долларов/екр</td></tr><tr><td valign="top">
<input type="checkbox" name="services[]" value="Ком. отдел: сброс второго пароля">Сброс второго пароля
</td><td valign="top" style="padding-top:4px;">15 долларов</td></tr><tr><td valign="top">
<input type="checkbox" name="services[]" value="Ком. отдел: смена пароля на банковский счёт">Смена пароля на банковский счёт
</td><td valign="top" style="padding-top:4px;">15 долларов/екр</td></tr><tr><td valign="top">
<input type="checkbox" name="services[]" value="Ком. отдел: смена пола персонажа">Смена пола персонажа
</td><td valign="top" style="padding-top:4px;">15 долларов</td></tr><tr><td valign="top">
<input type="checkbox" name="services[]" value="Ком. отдел: возврат вещи (продана или выкинута по ошибке)" />Возврат вещи (продана в магазин или выкинута по ошибке)    
</td><td valign="top" style="padding-top:4px;">25 долларов</td></tr><tr><td valign="top">
<input type="checkbox" name="services[]" value="Ком. отдел: снятие подарка с вещи (подарена по ошибке)" />Снятие подарка с вещи (подарена по ошибке) 
</td><td valign="top" style="padding-top:4px;">15 долларов</td></tr><td valign="top">
<input type="checkbox" name="services[]" value="Ком. отдел: экспресс проверка на чистоту" />Экспресс проверка на чистоту 
</td><td valign="top" style="padding-top:4px;">15 долларов</td></tr><td valign="top">
<input type="checkbox" name="services[]" value="Ком. отдел: обмен одной вещи персонажу" />Обмен одной обычной вещи персонажу 
</td><td valign="top" style="padding-top:4px;"><font color=red>20 долларов</font></td></tr><tr><td valign="top">
<input type="checkbox" name="services[]" value="Ком. отдел: обмен одной уникальной вещи персонажу" />Обмен одной уникальной вещи персонажу 
</td><td valign="top" style="padding-top:4px;"><font color=red>40 долларов</font></td></tr><tr><td valign="top">
<input type="checkbox" name="services[]" value="обмен уника 7,8,9,10 уровней на уник 11го уровня" />Обмен уника 7,8,9,10 уровней на уник 11го уровня
</td><td valign="top" style="padding-top:4px;">100 долларов</td></tr><td valign="top">
<input type="checkbox" name="services[]" value="покупка уника" />Покупка уника
</td><td valign="top" style="padding-top:4px;">200 долларов</td></tr><td valign="top">
<input type="checkbox" name="services[]" value="покупка уника 11 уровня" />Покупка уника 11 уровня
</td><td valign="top" style="padding-top:4px;">300 долларов</td></tr>
</table>
<font color=red><b>* Меняются только вещи возможные к покупке в Гос.магазине, Березе или Храмовой лавке. Раритетные вещи не подлежат обмену.</b></font>
<div class="item" style="padding-top:1px;"></div><table><tr><td><h1>Связь с представителем Ком.отдела по другому вопросу</h1>
</td></tr><tr><td>
<input type="checkbox" id="otherserv" name="services[]" value="Связь с представителем Ком.отдела по другому вопросу">Если вы не нашли необходимой вам услуги в списке, либо хотите связаться с представителем Ком.отдела по другим вопросам, отметьте тут и опишите свой вопрос в форме ниже.
</td></tr>
</table>
<div class="item" style="padding-top:1px;"></div>

<table>
<tr><td><h1>Email</h1></td></tr>
<tr><td>
<font color=red>*</font> Введите email адрес для связи: <input type="text" id="comemail" name="comemail" value="">
</td></tr>
</table>

<div class="item" style="padding-top:1px;"></div>

<table>
<tr><td><h1>Комментарии</h1></td></tr>
<tr><td>
<textarea name="comment" style="width: 453px;" rows="6" id="commentarea"></textarea>
</td></tr>
</table>

<table>
<tr><td style="width: inherit;"><a href="#" class="button" onClick="CheckComment();" />Заказ</a></td></tr>
</table>
<?
}

}
?>