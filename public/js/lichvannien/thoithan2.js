//
// Thời Gia Thời Thần
// Author: Harry Tran (Thiên Y) email: thien.y@operamail.com
//

// Hoàng Hắc Đạo
var H_H_DAO = new Array(
    'Thanh Long',
    'Minh Đường',
    'Thiên Hình',
    'Chu Tước',
    'Kim Quỹ',
    'Thiên Đức',
    'Bạch Hổ',
    'Ngọc Đường',
    'Thiên Lao',
    'Huyền Vũ',
    'Tư Mệnh',
    'Câu Trần');

// Hoàng Đạo Cát Thần
var H_D_CT = new Array(
    'nguyệt tiên, phúc đức',
    'thiên đức, bảo quang',
    'thiên khai, thiếu vi',
    'nhật tiên, phượng liễn',
    'thiên quý, thái ất',
    'minh phụ, quý nhân');

// Hắc Đạo Hung Thần
var H_D_HT = new Array(
    ' ', ' ',
    'thiên hình',
    'thiên tụng',
    ' ', ' ',
    'thiên sát',
    ' ',
    'tỏa thần',
    'thiên ngục',
    ' ',
    'địa ngục');

// Thời Hoàng Đạo
function layHHD(chi_vi) {
    var i = chi_vi % 6;
    var T;
    switch (i) {
        case 0:
            T = [5, 6, 7, 8, 9, 10, 11, 12, 1, 2, 3, 4];
            break;
        case 1:
            T = [3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 1, 2];
            break;
        case 2:
            T = [1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12];
            break;
        case 3:
            T = [11, 12, 1, 2, 3, 4, 5, 6, 7, 8, 9, 10];
            break;
        case 4:
            T = [9, 10, 11, 12, 1, 2, 3, 4, 5, 6, 7, 8];
            break;
        case 5:
            T = [7, 8, 9, 10, 11, 12, 1, 2, 3, 4, 5, 6];
            break;
    }
    return T;
}

// Thời Hoàng Đạo
function layTHD(chi_vi) {
    var i = chi_vi % 6;
    var T;
    switch (i) {
        case 0:
            T = [1, 2, 0, 3, 0, 0, 4, 0, 5, 6, 0, 0];
            break;
        case 1:
            T = [0, 0, 1, 2, 0, 3, 0, 0, 4, 0, 5, 6];
            break;
        case 2:
            T = [5, 6, 0, 0, 1, 2, 0, 3, 0, 0, 4, 0];
            break;
        case 3:
            T = [4, 0, 5, 6, 0, 0, 1, 2, 0, 3, 0, 0];
            break;
        case 4:
            T = [0, 0, 4, 0, 5, 6, 0, 0, 1, 2, 0, 3];
            break;
        case 5:
            T = [0, 3, 0, 0, 4, 0, 5, 6, 0, 0, 1, 2];
            break;
    }
    return T;
}

// Quí Đăng Thiên Môn 貴登天門
// cát thời và có khả năng giải hung thời
function quiDangThienMon(k, nn) {
    var can_vi = canVi(ThienCan(nn));
    var q = [-1, -1];
    switch (k) { // 1: vũ thủy - xuân phân; 2: xuân phân - cốc vũ
        case 1:
            switch (can_vi) { // mão dậu, tuất, hợi, sửu, mão dậu, dần, dần mão, thân, mùi, tị 
                case 0:
                case 4:
                    q = [3, 9];
                    break; // mão dậu
                case 1:
                    q = [10, -1];
                    break; // tuất
                case 2:
                    q = [11, -1];
                    break; // hợi
                case 3:
                    q = [1, -1];
                    break; // sửu
                case 5:
                    q = [2, -1];
                    break; // dần
                case 6:
                    q = [2, 3];
                    break; // dần mão 
                case 7:
                    q = [8, -1];
                    break; // thân
                case 8:
                    q = [7, -1];
                    break; // mùi
                case 9:
                    q = [5, -1];
                    break; // tị 
            }
            break;
        case 2:
            switch (can_vi) { // _x_, dậu, tuất, tý, dần thân, sửu dậu, dần thân, mão mùi, ngọ, thìn  
                case 0:
                    break;
                case 1:
                    q = [9, -1];
                    break; // dậu
                case 2:
                    q = [10, -1];
                    break; // tuất
                case 3:
                    q = [0, -1];
                    break; // tý
                case 4:
                case 6:
                    q = [2, 8];
                    break; // dần thân
                case 5:
                    q = [1, 9];
                    break; // sửu dậu
                case 7:
                    q = [3, 7];
                    break; // mão mùi 
                case 8:
                    q = [6, -1];
                    break; // ngọ
                case 9:
                    q = [4, -1];
                    break; // thìn  
            }
            break;
        case 3:
            switch (can_vi) { // _x_, _x_, _x_, dậu hợi, sửu mùi, tý thân, sửu mùi, dần ngọ, tị, mão  
                case 0:
                case 1:
                case 2:
                    break;
                case 3:
                    q = [9, 11];
                    break; // dậu hợi
                case 4:
                case 6:
                    q = [1, 7];
                    break; // sửu mùi
                case 5:
                    q = [0, 8];
                    c = 1;
                    break; // tý thân 
                case 7:
                    q = [2, 6];
                    break; // dần ngọ 
                case 8:
                    q = [5, -1];
                    break; // tị
                case 9:
                    q = [3, -1];
                    break; // mão
            }
            break;
        case 4:
            switch (can_vi) { // _x_, _x_, tuất, thân tuất, tý ngọ, mùi hợi, tý ngọ, sửu tị, dần thìn, dần  
                case 0:
                case 1:
                    break;
                case 2:
                    q = [10, -1];
                    break; // tuất
                case 3:
                    q = [8, 10];
                    break; // thân tuất
                case 4:
                case 6:
                    q = [0, 6];
                    break; // tý ngọ
                case 5:
                    q = [7, 11];
                    break; // mùi hợi 
                case 7:
                    q = [1, 5];
                    break; // sửu tị 
                case 8:
                    q = [2, 4];
                    break; // dần thìn 
                case 9:
                    q = [2, -1];
                    break; // dần 
            }
            break;
        case 5:
            switch (can_vi) { // _x_, tuất, dậu, mùi, tị hợi, ngọ tuất, tị hợi, tý thìn, sửu mão, _x_ 
                case 0:
                case 9:
                    break;
                case 1:
                    q = [10, -1];
                    break; // tuất
                case 2:
                    q = [9, -1];
                    break; // dậu
                case 3:
                    q = [7, -1];
                    break; // mùi
                case 4:
                case 6:
                    q = [5, 11];
                    break; // tị hợi
                case 5:
                    q = [6, 10];
                    break; // ngọ tuất
                case 7:
                    q = [0, 4];
                    break; // tý thìn 
                case 8:
                    q = [1, 3];
                    break; // sửu mão 
            }
            break;
        case 6:
            switch (can_vi) { // _x_, dậu, thân, ngọ, thìn tuất, tị, thìn tuất, mão hợi, tý dần, dần 
                case 0:
                    break;
                case 1:
                    q = [9, -1];
                    break; // dậu
                case 2:
                    q = [8, -1];
                    break; // thân
                case 3:
                    q = [6, -1];
                    break; // ngọ
                case 4:
                case 6:
                    q = [4, 10];
                    break; // thìn tuất
                case 5:
                    q = [5, -1];
                    break; // tị
                case 7:
                    q = [3, 11];
                    break; // mão hợi 
                case 8:
                    q = [0, 2];
                    break; // tý dần
                case 9:
                    q = [2, -1];
                    break; // dần 
            }
            break;
        case 7:
            switch (can_vi) { // dậu, thân, mùi, tị, mão dậu, thìn, mão dậu, tuất, hợi, sửu  
                case 0:
                    q = [9, -1];
                    break; // dậu
                case 1:
                    q = [8, -1];
                    break; // thân
                case 2:
                    q = [7, -1];
                    break; // mùi
                case 3:
                    q = [5, -1];
                    break; // tị
                case 4:
                case 6:
                    q = [3, 10];
                    break; // mão dậu
                case 5:
                    q = [4, -1];
                    break; // thìn
                case 7:
                    q = [10, -1];
                    break; // tuất
                case 8:
                    q = [11, -1];
                    break; // hợi
                case 9:
                    q = [1, -1];
                    break; // sửu 
            }
            break;
        case 8:
            switch (can_vi) { // dần thân, mão mùi, ngọ, thìn, _x_, mão, _x_, dậu, tuất, tý 
                case 0:
                    q = [2, 8];
                    break; // dần thân
                case 1:
                    q = [3, 7];
                    break; // mão mùi
                case 2:
                    q = [6, -1];
                    break; // ngọ
                case 3:
                    q = [4, -1];
                    break; // thìn
                case 4:
                case 6:
                    break;
                case 5:
                    q = [3, -1];
                    break; // mão
                case 7:
                    q = [9, -1];
                    break; // dậu
                case 8:
                    q = [10, -1];
                    break; // tuất
                case 9:
                    q = [0, -1];
                    break; // tý 
            }
            break;
        case 9:
            switch (can_vi) { // sửu mùi, dần ngọ, mão tị, mão, _x_, _x_, _x_, _x_, dậu, dậu hợi 
                case 0:
                    q = [1, 7];
                    break; // sửu mùi
                case 1:
                    q = [2, 6];
                    break; // dần ngọ
                case 2:
                    q = [3, 5];
                    break; // mão tị
                case 3:
                    q = [3, -1];
                    break; // mão
                case 4:
                case 5:
                case 6:
                case 7:
                    break;
                case 8:
                    q = [9, -1];
                    break; // dậu
                case 9:
                    q = [9, 11];
                    break; // dậu hợi
            }
            break;
        case 10:
            switch (can_vi) { // tý ngọ, sửu tị, dần thìn, _x_, _x_, _x_, _x_, _x_, _x_, thân tuất  
                case 0:
                    q = [0, 6];
                    break; // tý ngọ
                case 1:
                    q = [1, 5];
                    break; // sửu tị
                case 2:
                    q = [2, 4];
                    break; // dần thìn
                case 3:
                case 4:
                case 5:
                case 6:
                case 7:
                case 8:
                    break;
                case 9:
                    q = [8, 10];
                    break; // thân tuất
            }
            break;
        case 11:
            switch (can_vi) { // tị hợi, tý thìn, sửu, mão, _x_, thìn, _x_, _x_, _x_, mùi dậu 
                case 0:
                    q = [5, 11];
                    break; // tị hợi
                case 1:
                    q = [0, 4];
                    break; // tý thìn
                case 2:
                    q = [1, -1];
                    break; // sửu 
                case 2:
                    q = [3, -1];
                    break; // mão
                case 4:
                case 6:
                case 7:
                case 8:
                    break;
                case 5:
                    q = [4, -1];
                    break; // thìn
                case 9:
                    q = [7, 9];
                    break; // mùi dậu
            }
            break;
        case 12:
            switch (can_vi) { // thìn tuất, mão hợi, tý, dần, _x_, mão, _x_, _x_, thân, ngọ thân
                case 0:
                    q = [4, 10];
                    break; // thìn tuất
                case 1:
                    q = [3, 11];
                    break; // mão hợi
                case 2:
                    q = [0, -1];
                    break; // tý
                case 2:
                    q = [2, -1];
                    break; // dần
                case 4:
                case 6:
                case 7:
                    break;
                case 5:
                    q = [3, -1];
                    break; // mão
                case 8:
                    q = [8, -1];
                    break; // thân
                case 9:
                    q = [6, 8];
                    break; // ngọ thân
            }
            break;
    }
    return q;
}

// Tứ Đại Cát Thời 四大吉時
//  mạnh nguyệt giáp bính canh nhâm thời tức: tý ngọ mão dậu 
//  trọng nguyệt cấn tốn khôn kiền thời tức: dần thân tị hợi
//  quý nguyệt ất tân đinh quý thời tức thìn tuất sửu mùi 
// cát thời và có khả năng giải hung thời
function tuDaiCatThoi(k) {
    var t;
    switch (k) { // 1: vũ thủy - xuân phân; 2: xuân phân - cốc vũ
        case 1:
        case 4:
        case 7:
        case 10:
            t = [0, 3, 6, 9];
            break; //  mạnh nguyệt giáp bính canh nhâm thời tức tý ngọ mão dậu
        case 2:
        case 5:
        case 8:
        case 11:
            t = [2, 5, 8, 11];
            break; //  trọng nguyệt cấn tốn khôn kiền thời tức dần thân tị hợi
        case 3:
        case 6:
        case 9:
        case 12:
            t = [1, 4, 7, 10];
            break; //  quý nguyệt ất tân đinh quý thời tức thìn tuất sửu mùi
    }
    return t;
}

// Hỷ Thần 喜神 (DGTNH-18)
// giáp kỷ nhật cấn phương, dần thời; 
// ất canh nhật kiền phương, tuất thời; 
// bính tân nhật khôn phương, thân thời; 
// đinh nhâm nhật ly phương, ngọ thời; 
// mậu quý nhật tốn phương, thìn thời.
function thoiHyThan(can) {
    var h;
    switch (can) {
        case 0:
        case 5:
            h = 2;
            break; // dần
        case 1:
        case 6:
            h = 10;
            break; // tuất
        case 2:
        case 7:
            h = 8;
            break; // thân
        case 3:
        case 8:
            h = 6;
            break; // ngọ
        case 4:
        case 9:
            h = 4;
            break; // thìn
    }
    return h;
}

// Thiên Ất Quí Nhân 天乙貴人 (ngày gặp giờ)
// 古诀“甲戊庚牛羊，乙己鼠猴乡，丙丁猪鸡位，壬癸兔蛇藏，六辛逢虎马，此是贵人方”
// Giáp Mậu Canh Ngưu Dương, Ất Kỷ Thử Hầu hương, 
// Bính Đinh Trư Kê vị, Nhâm Quí Thố Xà tàng,
// Lục Tân phùng Mã Hổ, thữ thị Quí Nhân phương.
function thoiThienAtQuiNhan(can) {
    var c;
    switch (can) {
        case 0:
        case 4:
        case 6:
            c = [1, 7];
            break;
        case 1:
        case 5:
            c = [0, 8];
            break;
        case 2:
        case 3:
            c = [9, 11];
            break;
        case 7:
            c = [2, 6];
            break;
        case 8:
        case 9:
            c = [3, 5];
            break;
    }
    return c;
}

// Thiên Quan Quí Nhân 天官貴人
function thoiThienQuanQuiNhan(can) {
    var c;
    switch (can) {
        case 0:
            c = [9, -1];
            break; // giáp nhật dậu thời 
        case 1:
            c = [8, -1];
            break; // ất nhật thân thời 
        case 2:
            c = [0, -1];
            break; // bính nhật tý thời 
        case 3:
            c = [11, -1];
            break; // đinh nhật hợi thời 
        case 4:
            c = [3, -1];
            break; // mậu nhật mão thời 
        case 5:
            c = [2, -1];
            break; // kỷ nhật dần thời 
        case 6:
            c = [6, -1];
            break; // canh nhật ngọ thời 
        case 7:
            c = [5, -1];
            break; // tân nhật tị thời 
        case 8:
            c = [1, 7];
            break; // nhâm nhật sửu, mùi thời 
        case 9:
            c = [4, 10];
            break; // quý nhật thìn, tuất thời 
    }
    return c;
}

// Phúc Tinh Quý Nhân  福星貴人
// nhật can sanh thời can
// giáp nhật, bính dần thời
// ất nhật, đinh sửu thời & đinh hợi thời
// bính nhật, mậu tý & mậu tuất thời
// đinh nhật, kỷ dậu thời
// mậu nhật, canh thân thời
// kỷ nhật, tân mùi thời
// canh nhật, nhâm ngọ thời
// tân nhật, quý tị thời
// nhâm nhật, giáp thìn thời
// quý nhật, ất mão thời
/* 以年干或日干為主，對應於年月日時地支。凡甲、丙干見寅或子，乙、癸干見卯或丑，戊干見申，己干見未，丁干見亥，庚干見午，辛干見巳，壬干見辰，都是福星貴人。
以年干或日干为主. 凡甲丙两干见寅或子, 乙癸两干见卯或丑, 戊干见申, 己干见未, 丁干见亥, 庚干见午, 辛干见巳, 壬干见辰是也.人命若带福星, 主一生福禄无缺, 格局配合得当, 必然多福多寿, 金玉满堂
giáp ất bính đinh mậu kỷ canh tân nhâm quý
tý dần, sửu mão, tý dần, (dậu ?) hợi, thân, mùi, ngọ, tị, thìn, sửu mão
*/
function thoiPhucTinhQuiNhan(can) {
    var p = -1;
    var c;
    var TC = CAN[can];
    switch (can) {
        case 0:
            c = 2;
            if (ThoiCan(TC, 2) == CAN[c]) p = 2;
            break; // giáp nhật, bính dần thời
        case 1:
            c = 3;
            if (ThoiCan(TC, 1) == CAN[c]) p = 1;
            else if (ThoiCan(TC, 11) == CAN[c]) p = 11;
            break; // ất nhật, đinh sửu thời & đinh hợi thời 
        case 2:
            c = 4;
            if (ThoiCan(TC, 0) == CAN[c]) p = 0;
            else if (ThoiCan(TC, 10) == CAN[c]) p = 10;
            break; // bính nhật, mậu tý & mậu tuất thời 
        case 3:
            c = 5;
            if (ThoiCan(TC, 9) == CAN[c]) p = 9;
            break; // đinh nhật, kỷ dậu thời  
        case 4:
            c = 6;
            if (ThoiCan(TC, 8) == CAN[c]) p = 8;
            break; // mậu nhật, canh thân thời 
        case 5:
            c = 7;
            if (ThoiCan(TC, 7) == CAN[c]) p = 7;
            break; // kỷ nhật, tân mùi thời 
        case 6:
            c = 8;
            if (ThoiCan(TC, 6) == CAN[c]) p = 6;
            break; // canh nhật, nhâm ngọ thời 
        case 7:
            c = 9;
            if (ThoiCan(TC, 5) == CAN[c]) p = 5;
            break; // tân nhật, quý tị thời 
        case 8:
            c = 0;
            if (ThoiCan(TC, 4) == CAN[c]) p = 4;
            break; // nhâm nhật, giáp thìn thời  
        case 9:
            c = 1;
            if (ThoiCan(TC, 3) == CAN[c]) p = 3;
            break; // quý nhật, ất mão thời 
            /*
              //var p=[-1,-1];
              case 0: p=[0, 2]; break; // giáp nhật tý dần thời 
              case 1: p=[1, 3]; break; // ất nhật sửu mão thời 
              case 2: p=[0, 2]; break; // bính nhật tý dần thời 
              case 3: p=[9, 11]; break; // đinh nhật dậu hợi thời (dậu ?)
              case 4: p=[8,-1]; break; // mậu nhật thân thời 
              case 5: p=[7,-1]; break; // kỷ nhật mùi thời 
              case 6: p=[6,-1]; break; // canh nhật ngọ thời 
              case 7: p=[5,-1]; break; // tân nhật tị thời 
              case 8: p=[4, 7]; break; // nhâm nhật thìn thời 
              case 9: p=[1, 3]; break; // quý nhật sửu mão thời 
            */
    }

    return p;
}

// Thiên Xá 天赦
// nhật: giáp ất bính đinh mậu kỷ canh tân nhâm quý 
// thời: mão, sửu hợi, dậu, mùi, tỵ, mão, sửu hợi, dậu, mùi, tỵ
function thoiThienXa(can) {
    var h;
    switch (can) {
        case 0:
        case 5:
            h = [3, -1];
            break; // mão
        case 1:
        case 6:
            h = [1, 11];
            break; // sửu hợi
        case 2:
        case 7:
            h = [9, -1];
            break; // dậu
        case 3:
        case 8:
            h = [7, -1];
            break; // mùi
        case 4:
        case 9:
            h = [5, -1];
            break; // tỵ
    }
    return h;
}

// can hợp cát thời 干合吉時 = Ngũ Hợp
// nhật: giáp ất bính đinh mậu kỷ canh tân nhâm quý 
// thời: kỷ canh tân nhâm quý giáp ất bính đinh mậu 
function thoiNguHop(can) {
    var c = can + 5;
    var h = -1;
    if (c > 9) c -= 10;
    for (var i = 0; i < 12; i++) {
        if (CAN[c] == ThoiCan(CAN[can], i)) {
            h = i;
            break;
        }
    }
    return h;
}

// Can Hợp Cát Thời 干合吉時
function thoiCanHop(ngay, gio) {
    var c = 0;

    /*
    switch(ngay)
    {
    case 0: if (gio==5) c=1; break; // kỷ
    case 1: if (gio==6) c=1; break; // canh
    case 2: if (gio==7) c=1; break; // tân
    case 3: if (gio==8) c=1; break; // nhâm
    case 4: if (gio==9) c=1; break; // quý
    case 5: if (gio==0) c=1; break; // giáp
    case 6: if (gio==1) c=1; break; // ất
    case 7: if (gio==2) c=1; break; // bính
    case 8: if (gio==3) c=1; break; // đinh
    case 9: if (gio==4) c=1; break; // mậu
    }
    */
    c = ngay - gio;
    if (c < 0) c = -c;

    return (c == 5 ? 1 : 0);
}

// dương quý ca viết:
// canh mậu kiến ngưu giáp tại dương,
// ất hầu kỷ thử bính kê phương,
// đinh trư quý xà nhâm thị thỏ,
// lục tân phùng hổ quý vi dương.
// dương quý: mùi thân dậu hợi sửu tý sửu dần mão tị 
function thoiDuongQuy(can) {
    var q;

    switch (can) {
        case 0:
            q = 7;
            break; // mùi 
        case 1:
            q = 8;
            break; // thân 
        case 2:
            q = 9;
            break; // dậu 
        case 3:
            q = 11;
            break; // hợi 
        case 4:
            q = 1;
            break; // sửu 
        case 5:
            q = 0;
            break; // tý 
        case 6:
            q = 1;
            break; // sửu 
        case 7:
            q = 2;
            break; // dần 
        case 8:
            q = 3;
            break; // mão 
        case 9:
            q = 5;
            break; // tị 
    }
    return q;
}

// âm quý ca viết:
// giáp quý âm ngưu canh mậu dương,
// ất quý tại thử kỷ hầu hương,
// bính trư đinh kê tân ngộ mã,
// nhâm xà quý thỏ chúc âm phương.
// âm quý: sửu tý hợi dậu mùi thân mùi ngọ tị mão 
function thoiAmQuy(can) {
    var q;

    switch (can) {
        case 0:
            q = 1;
            break; // sửu 
        case 1:
            q = 0;
            break; // tý 
        case 2:
            q = 11;
            break; // hợi 
        case 3:
            q = 9;
            break; // dậu 
        case 4:
            q = 7;
            break; // mùi 
        case 5:
            q = 8;
            break; // thân 
        case 6:
            q = 7;
            break; // mùi 
        case 7:
            q = 6;
            break; // ngọ 
        case 8:
            q = 5;
            break; // tị 
        case 9:
            q = 3;
            break; // mão 
    }
    return q;
}

// thái dương 太陽
// giáp ất bính đinh mậu kỷ canh tân nhâm quý
// mùi tý & dậu thìn thân mão mùi thân thìn thân mão 
function thoiThaiDuong(can) {
    var h;
    switch (can) {
        case 0:
        case 5:
            h = [7, -1];
            break; // mùi 
        case 1:
            h = [0, 9];
            break; // tý & dậu
        case 2:
        case 7:
            h = [4, -1];
            break; // thìn 
        case 3:
        case 8:
        case 6:
            h = [8, -1];
            break; // thân 
        case 4:
        case 9:
            h = [3, -1];
            break; // mão 
    }
    return h;
}

// thái âm 太陰
// giáp ất bính đinh mậu kỷ canh tân nhâm quý
// sửu tuất, ngọ, sửu tuất, dần hợi, ngọ, sửu tuất, tị, sửu tuất, dần hợi, ngọ
function thoiThaiAm(can) {
    var h;
    switch (can) {
        case 0:
        case 5:
            h = [1, 10];
            break; // sửu tuất 
        case 1:
        case 6:
            h = [6, -1];
            break; // ngọ
        case 2:
        case 7:
            h = [1, 10];
            break; // sửu tuất 
        case 3:
        case 8:
            h = [2, 11];
            break; // dần hợi
        case 4:
        case 9:
            h = [6, -1];
            break; // ngọ
    }
    return h;
}

// trường sinh 長生
// hợi ngọ dần dậu dần dậu tị tý thân mão 
function thoiTruongSinh(can) {
    var q;

    switch (can) {
        case 0:
            q = 11;
            break; // hợi 
        case 1:
            q = 6;
            break; // ngọ 
        case 2:
        case 4:
            q = 2;
            break; // dần 
        case 3:
        case 5:
            q = 9;
            break; // dậu 
        case 6:
            q = 5;
            break; // tị 
        case 7:
            q = 0;
            break; // tý 
        case 8:
            q = 8;
            break; // thân 
        case 9:
            q = 3;
            break; // mão 
    }
    return q;
}

// văn tinh 文星
// Giáp & Ất (mộc) với tý & hợi thủy là tương sinh
// Bính & Đinh (hỏa) với dần & mão mộc là tương sinh
// Mậu & Kỷ (thổ) với ngọ & tỵ hỏa là tương sinh
// Canh & Tân (kim) với thìn, mùi, tuất & sửu thổ là tương sinh
// Nhâm & Quí (thủy) với thân & dậu kim là tương sinh
function thoiVanTinh(can) {
    var q;

    switch (can) {
        case 0:
        case 1:
            q = 11;
            break; // tý & hợi [ thủy sinh mộc ]
        case 2:
        case 3:
            q = 2;
            break; // dần & mão [ mộc sinh hỏa ]
        case 4:
        case 5:
            q = 6;
            break; // ngọ & tỵ [ hỏa sinh thổ ]
        case 6:
        case 7:
            q = 7;
            break; // thìn, mùi, tuất & sửu [ thổ sinh kim ]
        case 8:
        case 9:
            q = 8;
            break; // thân & dậu [ kim sinh thủy ]
    }
    return q;
}

// đế vượng 帝旺
// mão dần ngọ tị ngọ tị dậu thân tý hợi
function thoiDeVuong(can) {
    var q;

    switch (can) {
        case 0:
            q = 3;
            break; // mão 
        case 1:
            q = 2;
            break; // dần 
        case 2:
        case 4:
            q = 6;
            break; // ngọ 
        case 3:
        case 5:
            q = 5;
            break; // tị 
        case 6:
            q = 9;
            break; // dậu 
        case 7:
            q = 8;
            break; // thân 
        case 8:
            q = 0;
            break; // tý 
        case 9:
            q = 11;
            break; // hợi
    }
    return q;
}

// la thiên đại tiến [?= thai (thủ kì thai thời vi đại tiến)]
// 羅天進時: 甲己戊癸進子時。乙庚逢卯丙辛午。丁壬逢酉為大進。凡事逢之大吉利。
// 罗天大进时：
// 甲己日寻子时良，乙庚日逢卯时昌，
// 丁壬日酉富贵长，丙辛日在午时上， 
// 戊癸二日子时福，百事逢此富贵长。
// giáp kỷ nhật tầm tý thời lương ，ất canh nhật phùng mão thời xương ，
// đinh nhâm nhật dậu phú quý trường ，bính tân nhật tại ngọ thời thượng ， 
// mậu quý nhị nhật tý thời phúc ，bách sự phùng thử phú quý trường 。
// Thi lệ: giáp kỷ mậu quý tiến tý thời, ất canh phùng mão bính tân ngọ, đinh nhâm phùng dậu vi đại tiến, phàm sự phùng chi đại cát lợi.
// dậu thân tý hợi tý hợi mão dần ngọ tị 
function thoiLaThienDaiTien(can) {
    var q;

    switch (can) {
        case 0:
        case 4:
        case 5:
        case 9:
            q = 0;
            break; // tý 
        case 1:
        case 6:
            q = 3;
            break; // mão
        case 2:
        case 7:
            q = 6;
            break; // ngọ 
        case 3:
        case 8:
            q = 9;
            break; // dậu 
    }
    return q;
}

// la thiên đại thoái [? = lâm quan (lâm quan thời vi đại thoái)]
// 羅天退時: 甲己戊癸退巳時。乙庚申上丙辛亥。丁壬逢寅臨官位。格局有氣反吉在。
// Thi lệ: giáp kỷ mậu quý thoái tị thời, ất canh thân thượng bính tân hợi, đinh nhâm phùng dần lâm quan vị, cách cục hữu khí phản cát tại.
function thoiLaThienDaiThoai(can) {
    var q; // phù

    switch (can) {
        case 0:
        case 4:
        case 5:
        case 9:
            q = 5;
            break; // tị
        case 1:
        case 6:
            q = 8;
            break; // thân
        case 2:
        case 7:
            q = 11;
            break; // hợi
        case 3:
        case 8:
            q = 2;
            break; // dần
    }
    return q;
}

// Ngũ Phù 五符 Trạch Thời (DGTNH)
// dụng nhật lộc khởi ngũ phù thuận bố thập nhị vị, kì pháp bất quá nhật can lâm thời chi;
// dĩ lâm quan, (thai, dưỡng) thời vi cát, dư tịnh hung nhĩ. (thai, dưỡng loại bỏ!) 
//   Giáp-mộc (dương-thuận hành): trường sanh ở hợi, mộc dục ở tý, quan đái ở sửu, lâm quan ở dần, đế vượng ở mão, 
//     suy ở thần, bệnh ở tị, tử ở ngọ, mộ ở mùi, tuyệt ở thân, thai ở dậu, dưỡng ở tuất.
//   giáp lâm quan tại dần, ất lâm quan mão,
//   bính mậu lâm quan tị,  đinh kỷ lâm quan ngọ,
//   canh lâm quan tại thân, tân lâm quan tại dậu,
//   nhâm lâm quan tại hợi, quý lâm quan tại tý
// lộc nguyên: dần mão tị ngọ tị ngọ thân dậu hợi tý 
function thoiNguPhu(can) {
    var p; // phù

    switch (can) {
        case 0:
            p = 2;
            break; // dần 
        case 1:
            p = 3;
            break; // mão 
        case 2:
        case 4:
            p = 5;
            break; // tị 
        case 3:
        case 5:
            p = 6;
            break; // ngọ 
        case 6:
            p = 8;
            break; // thân 
        case 7:
            p = 9;
            break; // dậu 
        case 8:
            p = 11;
            break; // hợi 
        case 9:
            p = 0;
            break; // tý
    }
    return p;
}

// đường phù 唐符 (=phi nhận 飛刃)
// giáp ất bính đinh mậu kỷ canh tân nhâm quý
// dậu tuất tý sửu tý sửu mão thìn ngọ mùi 
function thoiDuongPhu(can) {
    var p; // phù

    switch (can) {
        case 0:
            p = 9;
            break; // dậu 
        case 1:
            p = 10;
            break; // tuất 
        case 2:
        case 4:
            p = 0;
            break; // tý 
        case 3:
        case 5:
            p = 1;
            break; // sửu 
        case 6:
            p = 3;
            break; // mão 
        case 7:
            p = 4;
            break; // thìn 
        case 8:
            p = 6;
            break; // ngọ 
        case 9:
            p = 7;
            break; // mùi 
    }
    return p;
}

// quốc ấn 國印
// giáp ất bính đinh mậu kỷ canh tân nhâm quý
// tuất hợi sửu dần sửu dần thìn tị mùi thân 
function thoiQuocAn(can) {
    var p; // phù

    switch (can) {
        case 0:
            p = 10;
            break; // tuất 
        case 1:
            p = 11;
            break; // hợi 
        case 2:
        case 4:
            p = 1;
            break; // sửu 
        case 3:
        case 5:
            p = 2;
            break; // dần 
        case 6:
            p = 4;
            break; // thìn 
        case 7:
            p = 5;
            break; // tị 
        case 8:
            p = 7;
            break; // mùi 
        case 9:
            p = 8;
            break; // thân 
    }
    return p;
}

// bát lộc 八祿 = lộc nguyên = lâm quan
// giáp ất bính đinh mậu kỷ canh tân nhâm quý 
// dần mão tị ngọ tị ngọ thân dậu hợi tý 
function thoiBatLoc(can) {
    var p;

    switch (can) {
        case 0:
            p = 2;
            break; // dần 
        case 1:
            p = 3;
            break; // mão 
        case 2:
        case 4:
            p = 5;
            break; // tị 
        case 3:
        case 5:
            p = 6;
            break; // ngọ 
        case 6:
            p = 8;
            break; // thân 
        case 7:
            p = 9;
            break; // dậu 
        case 8:
            p = 11;
            break; // hợi 
        case 9:
            p = 0;
            break; // tý 
    }
    return p;
}

// thủy tinh 水星
// giáp ất bính đinh mậu kỷ canh tân nhâm quý
// tý dậu, mùi, dần hợi, sửu tuất, tị, tý dậu, ngọ, dần hợi, sửu tuất, tị 
// đông thiên dụng thủy tinh
function thoiThuyTinh(can) {
    var c;
    switch (can) {
        case 0:
        case 5:
            c = [0, 9];
            break; // tý dậu
        case 1:
            c = [7, -1];
            break; // mùi
        case 6:
            c = [6, -1];
            break; // ngọ
        case 2:
        case 7:
            c = [2, 11];
            break; // dần hợi
        case 3:
        case 8:
            c = [1, 10];
            break; // sửu tuất
        case 4:
        case 9:
            c = [5, -1];
            break; //  tị 
    }
    return c;
}

// mộc tinh 木星
// giáp ất bính đinh mậu kỷ canh tân nhâm quý
// dần hợi, tị, tý dậu, mão, mùi, dần hợi, thìn, tý dậu, mão, mùi 
// xuân thiên dụng mộc tinh
function thoiMocTinh(can) {
    var c;
    switch (can) {
        case 0:
        case 5:
            c = [2, 11];
            break; // dần hợi
        case 1:
            c = [5, -1];
            break; // tị
        case 6:
            c = [4, -1];
            break; // thìn 
        case 2:
        case 7:
            c = [0, 9];
            break; // tý dậu
        case 3:
        case 8:
            c = [3, -1];
            break; // mão
        case 4:
        case 9:
            c = [7, -1];
            break; // mùi
    }
    return c;
}

// kim tinh 金星
// giáp ất bính đinh mậu kỷ canh tân nhâm quý
// ngọ, sửu tuất, tị, mùi, dần hợi, ngọ, tý dậu, tị, mùi, dần hợi 
// thu thiên dụng kim tinh
function thoiKimTinh(can) {
    var c;
    switch (can) {
        case 0:
        case 5:
            c = [6, -1];
            break; // ngọ
        case 1:
            c = [1, 10];
            break; // sửu tuất
        case 6:
            c = [0, 9];
            break; // tý dậu
        case 2:
        case 7:
            c = [5, -1];
            break; // tị
        case 3:
        case 8:
            c = [7, -1];
            break; // mùi
        case 4:
        case 9:
            c = [2, 11];
            break; //  dần hợi
    }
    return c;
}

// hỏa tinh 火星
// giáp ất bính đinh mậu kỷ canh tân nhâm quý
// thân, thân, mão, tý dậu, thìn, thân, mùi, mão, tý dậu, thìn 
// hạ thiên dụng hỏa tinh, la hầu
function thoiHoaTinh(can) {
    var c;
    switch (can) {
        case 0:
        case 1:
        case 5:
            c = [8, -1];
            break; // thân
        case 2:
        case 7:
            c = [3, -1];
            break; // mão
        case 3:
        case 8:
            c = [0, 9];
            break; // tý dậu
        case 4:
        case 9:
            c = [4, -1];
            break; //  thìn
        case 6:
            c = [7, -1];
            break; // mùi
    }
    return c;
}

// thổ tinh 土星
// giáp ất bính đinh mậu kỷ canh tân nhâm quý
// thìn, mão, mùi, tị, tý dậu, thìn, dần hợi, mùi, tị, tý dậu  
// tứ quý dụng thổ tinh, kế đô
function thoiThoTinh(can) {
    var c;
    switch (can) {
        case 0:
        case 5:
            c = [4, -1];
            break; // thìn
        case 1:
            c = [3, -1];
            break; // mão
        case 6:
            c = [2, 11];
            break; // dần hợi
        case 2:
        case 7:
            c = [7, -1];
            break; // mùi
        case 3:
        case 8:
            c = [5, -1];
            break; // tị  
        case 4:
        case 9:
            c = [0, 9];
            break; //  tý dậu 
    }
    return c;
}

// Thời Kiến  
// nhật: tý sửu dần mão thìn tị ngọ mùi thân dậu tuất hợi 
// thời kiến  tý sửu dần mão thìn tị ngọ mùi thân dậu tuất hợi 
function thoiKien(chi) {
    return chi;
}

// lục hợp 六合: sửu tý hợi tuất dậu thân mùi ngọ tị thìn mão dần
function thoiLucHop(chi) {
    var c;
    switch (chi) {
        case 0:
            c = 1;
            break;
        case 1:
            c = 0;
            break;
        case 2:
            c = 11;
            break;
        case 3:
            c = 10;
            break;
        case 4:
            c = 9;
            break;
        case 5:
            c = 8;
            break;
        case 6:
            c = 7;
            break;
        case 7:
            c = 6;
            break;
        case 8:
            c = 5;
            break;
        case 9:
            c = 4;
            break;
        case 10:
            c = 3;
            break;
        case 11:
            c = 2;
            break;
    }
    return c;
}

// tam hợp 三合: thân thìn, dậu tị, tuất ngọ, mùi hợi, tý thân, sửu dậu, tuất dần, mão hợi, thìn tý, sửu tị, ngọ dần, mùi mão 
function thoiTamHop(chi) {
    var c;
    switch (chi) {
        case 0:
            c = [4, 8];
            break; // thân thìn
        case 1:
            c = [5, 9];
            break; // dậu tị
        case 2:
            c = [6, 10];
            break; // tuất ngọ
        case 3:
            c = [7, 11];
            break; // mùi hợi
        case 4:
            c = [0, 8];
            break; // tý thân
        case 5:
            c = [1, 9];
            break; // sửu dậu
        case 6:
            c = [2, 10];
            break; // tuất dần
        case 7:
            c = [3, 11];
            break; // mão hợi
        case 8:
            c = [0, 4];
            break; // thìn tý
        case 9:
            c = [1, 5];
            break; // sửu tị
        case 10:
            c = [2, 6];
            break; // ngọ dần
        case 11:
            c = [3, 7];
            break; // mùi mão 
    }
    return c;
}

// dịch mã 驛馬
// tý sửu dần mão thìn tị ngọ mùi thân dậu tuất hợi 
// dần hợi thân tị dần hợi thân tị dần hợi thân tị 
function thoiDichMa(chi) {
    var c;
    switch (chi) {
        case 0:
        case 4:
        case 8:
            c = 2;
            break;
        case 1:
        case 5:
        case 9:
            c = 11;
            break;
        case 2:
        case 6:
        case 10:
            c = 8;
            break;
        case 3:
        case 7:
        case 11:
            c = 5;
            break;
    }
    return c;
}

// vũ khúc 武曲
// tý sửu dần mão thìn tị ngọ mùi thân dậu tuất hợi 
// sửu thìn, tuất, mùi, sửu thìn, tuất, mùi, sửu thìn, tuất, mùi, sửu thìn, tuất, mùi
function thoiVuKhuc(chi) {
    var c;
    switch (chi) {
        case 0:
        case 3:
        case 6:
        case 9:
            c = [1, 4];
            break; // sửu thìn
        case 1:
        case 4:
        case 7:
        case 10:
            c = [10, -1];
            break; // tuất
        case 2:
        case 5:
        case 8:
        case 11:
            c = [7, -1];
            break; // mùi
    }
    return c;
}

// tham lang 貪狼
// tý sửu dần mão thìn tị ngọ mùi thân dậu tuất hợi 
// dậu, ngọ, tý mão, dậu, ngọ, tý mão, dậu, ngọ, tý mão, dậu, ngọ, tý mão
function thoiThamLang(chi) {
    var c;
    switch (chi) {
        case 0:
        case 3:
        case 6:
        case 9:
            c = [9, -1];
            break; // dậu
        case 1:
        case 4:
        case 7:
        case 10:
            c = [6, -1];
            break; // ngọ
        case 2:
        case 5:
        case 8:
        case 11:
            c = [0, 3];
            break; // tý mão
    }
    return c;
}

// tả phụ 左輔
// tý sửu dần mão thìn tị ngọ mùi thân dậu tuất hợi 
// dần hợi, thân, tị, dần hợi, thân, tị, dần hợi, thân, tị, dần hợi, thân, tị
function thoiTaPhu(chi) {
    var c;
    switch (chi) {
        case 0:
        case 3:
        case 6:
        case 9:
            c = [2, 11];
            break; // dần hợi
        case 1:
        case 4:
        case 7:
        case 10:
            c = [8, -1];
            break; // thân
        case 2:
        case 5:
        case 8:
        case 11:
            c = [5, -1];
            break; // tị
    }
    return c;
}

// hữu bật 右弼
// tý sửu dần mão thìn tị ngọ mùi thân dậu tuất hợi 
// tuất, mùi, sửu thìn, tuất, mùi, sửu thìn, tuất, mùi, sửu thìn, tuất, mùi, sửu thìn 
function thoiHuuBat(chi) {
    var c;
    switch (chi) {
        case 0:
        case 3:
        case 6:
        case 9:
            c = [10, -1];
            break; // tuất
        case 1:
        case 4:
        case 7:
        case 10:
            c = [7, -1];
            break; // mùi
        case 2:
        case 5:
        case 8:
        case 11:
            c = [1, 4];
            break; // sửu thìn
    }
    return c;
}

// Tỷ Kiên 比肩
// Can ngày bằng can giờ
function thoiTyKien(nhat, thoi) // can
{
    var c = 0;
    if (nhat == thoi) c = 1;
    return c;
}

// ngũ bất ngộ 五不遇時
// giáp ất bính đinh mậu kỷ canh tân nhâm quý
// canh tân nhâm quý giáp ất bính đinh mậu kỷ 
function thoiNguBatNgo(can) {
    var c = can + 6;
    if (c > 9) c -= 10;
    return c;
}

// tỏa thần 鎖神 (hung) = Thiên Lao Hắc Đạo
// tý sửu dần mão thìn tị ngọ mùi thân dậu tuất hợi 
// thìn ngọ thân tuất tý dần thìn ngọ thân tuất tý dần 
function thoiToaThan(chi) {
    var c;
    switch (chi) {
        case 0:
        case 6:
            c = 4;
            break;
        case 1:
        case 7:
            c = 6;
            break;
        case 2:
        case 8:
            c = 8;
            break;
        case 3:
        case 9:
            c = 10;
            break;
        case 4:
        case 10:
            c = 0;
            break;
        case 5:
        case 11:
            c = 2;
            break;
    }
    return c;
}

// ngũ quỷ 五鬼
// giáp ất bính đinh mậu kỷ canh tân nhâm quý
// tị ngọ, dần mão, tý sửu, tuất hợi, thân dậu, tị ngọ, dần mão, tý sửu, tuất hợi, thân dậu
function thoiNguQuy(can) {
    var k;
    switch (can) {
        case 0:
        case 5:
            k = [5, 6];
            break; // tị ngọ
        case 1:
        case 6:
            k = [2, 3];
            break; // dần mão
        case 2:
        case 7:
            k = [0, 1];
            break; // tý sửu
        case 3:
        case 8:
            k = [10, 11];
            break; // tuất hợi
        case 4:
        case 9:
            k = [8, 9];
            break; // thân dậu
    }
    return k;
}

// la hầu 羅喉 (hung tinh cho nam chủ)
// giáp ất bính đinh mậu kỷ canh tân nhâm quý
// tị, dần hợi, ngọ, ngọ, sửu tuất, tị, sửu tuất, ngọ, ngọ, sửu tuất,
function thoiLaHau(can) {
    var h;
    switch (can) {
        case 0:
        case 5:
            h = [5, -1];
            break;
        case 1:
            h = [2, 11];
            break;
        case 2:
        case 3:
        case 7:
        case 8:
            h = [6, -1];
            break;
        case 4:
        case 6:
        case 9:
            h = [1, 10];
            break;
    }
    return h;
}

// kế đô 計都 (hung tinh cho nữ chủ)
// giáp ất bính đinh mậu kỷ canh tân nhâm quý
// mão thìn thân thìn thân mão mão thân thìn thân 
function thoiKeDo(can) {
    var k;
    switch (can) {
        case 0:
        case 5:
        case 6:
            k = 3;
            break;
        case 1:
        case 3:
        case 8:
            k = 4;
            break;
        case 2:
        case 4:
        case 7:
        case 9:
            k = 8;
            break;
    }
    return k;
}

// Lục Mậu: Mậu Tý . . .
function thoiLucMau(can) {
    var c = 4; // can Mậu
    var h = -1;
    for (var i = 0; i < 12; i++) {
        if (CAN[c] == ThoiCan(CAN[can], i)) {
            h = i;
            break;
        }
    }
    return h;
}

// nhật mộ:
// nhật can ngộ mộ khố thời.
// mùi tuất tuất sửu tuất sửu sửu thìn thìn mùi 
function thoiMoKho(can) {
    var p;

    switch (can) {
        case 0:
        case 9:
            p = 7;
            break; // mùi 
        case 1:
        case 2:
        case 4:
            p = 10;
            break; // tuất 
        case 3:
        case 5:
        case 6:
            p = 1;
            break; // sửu 
        case 7:
        case 8:
            p = 4;
            break; // thìn 
    }
    return p;
}

// thời phá
// tý sửu dần mão thìn tị ngọ mùi thân dậu tuất hợi 
// ngọ mùi thân dậu tuất hợi tý sửu dần mão thìn tị 
function thoiXungPha(chi) {
    var p;
    switch (chi) {
        case 0:
            p = 6;
            break;
        case 1:
            p = 7;
            break;
        case 2:
            p = 8;
            break;
        case 3:
            p = 9;
            break;
        case 4:
            p = 10;
            break;
        case 5:
            p = 11;
            break;
        case 6:
            p = 0;
            break;
        case 7:
            p = 1;
            break;
        case 8:
            p = 2;
            break;
        case 9:
            p = 3;
            break;
        case 10:
            p = 4;
            break;
        case 11:
            p = 5;
            break;
    }
    return p;
}

// thời hình (6 hình)
function thoi6Hinh(chi) {
    var LucHINH = [3, 10, 5, -1, 7, -1, 9, -1, 11, -1, -1, -1];
    // Tý hình Mão
    // Sửu hình Tuất
    // Dần hình Tỵ
    // Thìn hình Mùi
    // Ngọ hình Dậu
    // Thân hình Hợi
    if (chi >= CHI.length) return -1;
    return LucHINH[chi];
}

// thời hình (3 hình, nhị hình và tự hình)
// tý sửu dần mão thìn tị ngọ mùi thân dậu tuất hợi 
// mão tuất tị tý thìn thân ngọ sửu dần dậu mùi hợi 
function thoi3Hinh(chi) {
    var h;
    switch (chi) {
        case 0:
            h = 3;
            break;
        case 1:
            h = 10;
            break;
        case 2:
            h = 5;
            break;
        case 3:
            h = 0;
            break;
        case 4:
            h = 4;
            break;
        case 5:
            h = 8;
            break;
        case 6:
            h = 6;
            break;
        case 7:
            h = 1;
            break;
        case 8:
            h = 2;
            break;
        case 9:
            h = 9;
            break;
        case 10:
            h = 7;
            break;
        case 11:
            h = 11;
            break;
    }
    return h;
}

// thời hại
// tý sửu dần mão thìn tị ngọ mùi thân dậu tuất hợi 
// mùi ngọ tị thìn mão dần sửu tý hợi tuất dậu thân 
function thoi6Hai(chi) {
    var h = 7 - chi;
    if (h < 0) h += 12;
    return h;
}

// thiên cương 天罡
// tý sửu dần mão thìn tị ngọ mùi thân dậu tuất hợi 
// mão tuất tị tý mùi dần dậu thìn hợi ngọ sửu thân 
function thoiThienCuong(chi) {
    var h;
    switch (chi) {
        case 0:
            h = 3;
            break;
        case 1:
            h = 10;
            break;
        case 2:
            h = 5;
            break;
        case 3:
            h = 0;
            break;
        case 4:
            h = 7;
            break;
        case 5:
            h = 2;
            break;
        case 6:
            h = 9;
            break;
        case 7:
            h = 4;
            break;
        case 8:
            h = 11;
            break;
        case 9:
            h = 6;
            break;
        case 10:
            h = 1;
            break;
        case 11:
            h = 8;
            break;
    }
    return h;
}

// hà khôi 河魁
// tý sửu dần mão thìn tị ngọ mùi thân dậu tuất hợi 
// dậu thìn hợi ngọ sửu thân mão tuất tị tý mùi dần 
function thoiHaKhoi(chi) {
    var h;
    switch (chi) {
        case 0:
            h = 9;
            break;
        case 1:
            h = 4;
            break;
        case 2:
            h = 11;
            break;
        case 3:
            h = 6;
            break;
        case 4:
            h = 1;
            break;
        case 5:
            h = 8;
            break;
        case 6:
            h = 3;
            break;
        case 7:
            h = 10;
            break;
        case 8:
            h = 5;
            break;
        case 9:
            h = 0;
            break;
        case 10:
            h = 7;
            break;
        case 11:
            h = 2;
            break;
    }
    return h;
}

// cô thần 孤辰
// tý sửu dần mão thìn tị ngọ mùi thân dậu tuất hợi 
// tuất hợi tý sửu dần mão thìn tị ngọ mùi thân dậu 
function thoiCoThan(chi) {
    var h;
    switch (chi) {
        case 0:
            h = 10;
            break;
        case 1:
            h = 11;
            break;
        case 2:
            h = 0;
            break;
        case 3:
            h = 1;
            break;
        case 4:
            h = 2;
            break;
        case 5:
            h = 3;
            break;
        case 6:
            h = 4;
            break;
        case 7:
            h = 5;
            break;
        case 8:
            h = 6;
            break;
        case 9:
            h = 7;
            break;
        case 10:
            h = 8;
            break;
        case 11:
            h = 9;
            break;
    }
    return h;
}

// quả tú 寡宿
// tý sửu dần mão thìn tị ngọ mùi thân dậu tuất hợi 
// thìn tị ngọ mùi thân dậu tuất hợi tý sửu dần mão 
function thoiQuaTu(chi) {
    var h;
    switch (chi) {
        case 0:
            h = 4;
            break;
        case 1:
            h = 5;
            break;
        case 2:
            h = 6;
            break;
        case 3:
            h = 7;
            break;
        case 4:
            h = 8;
            break;
        case 5:
            h = 9;
            break;
        case 6:
            h = 10;
            break;
        case 7:
            h = 11;
            break;
        case 8:
            h = 0;
            break;
        case 9:
            h = 1;
            break;
        case 10:
            h = 2;
            break;
        case 11:
            h = 3;
            break;
    }
    return h;
}

// thiên cẩu hạ thực
// hợi tý sửu dần mão thìn tị ngọ mùi thân dậu tuất
function thoiThienCauHaThuc(chi) {
    var h;
    switch (chi) {
        case 0:
            h = 11;
            break;
        case 1:
            h = 0;
            break;
        case 2:
            h = 1;
            break;
        case 3:
            h = 2;
            break;
        case 4:
            h = 3;
            break;
        case 5:
            h = 4;
            break;
        case 6:
            h = 5;
            break;
        case 7:
            h = 6;
            break;
        case 8:
            h = 7;
            break;
        case 9:
            h = 8;
            break;
        case 10:
            h = 9;
            break;
        case 11:
            h = 10;
            break;
    }
    return h;
}

// Cổ Mộ Sát (Tri)
// Ngày Tý Ngọ Mão Dậu, kị giờ Tị,
// Ngày Thìn Tuất Sửu Mùi, kị giờ Sửu,
// Ngày Dần Thân Tị Hợi, kị  giờ Dậu,
// Ngày giờ trên không nên tu tạo mộ viên (tức hoa viên mồ mả) phạm nhằm kẻ làm và chủ đồng bị tổn hại.
function thoiCoMoSat(chi) {
    var k;
    switch (chi) {
        case 0:
        case 3:
        case 6:
        case 9:
            k = 5;
            break;
        case 1:
        case 4:
        case 7:
        case 10:
            k = 1;
            break;
        case 2:
        case 5:
        case 8:
        case 11:
            k = 9;
            break;
    }
    return k;
}

// thiên binh 天兵
// giáp ất bính đinh mậu kỷ canh tân nhâm quý
// dần, tý tuất, thân, ngọ, thìn, dần, tý tuất, thân, ngọ, thìn
// kị thượng lương, nhập liễm
function thoiThienBinh(can) {
    var c;
    switch (can) {
        case 0:
        case 5:
            c = [2, -1];
            break; // dần
        case 1:
        case 6:
            c = [0, 10];
            break; // tý tuất
        case 2:
        case 7:
            c = [8, -1];
            break; // thân
        case 3:
        case 8:
            c = [6, -1];
            break; // ngọ
        case 4:
        case 9:
            c = [4, -1];
            break; //  thìn
    }
    return c;
}

// địa binh 地兵
// giáp ất bính đinh mậu kỷ canh tân nhâm quý
// ngọ, thìn, dần, tý tuất, thân, ngọ, thìn, dần, tý tuất, thân
// kị phá thổ
function thoiDiaBinh(can) {
    var c;
    switch (can) {
        case 0:
        case 5:
            c = [6, -1];
            break; // ngọ 
        case 1:
        case 6:
            c = [4, -1];
            break; // thìn
        case 2:
        case 7:
            c = [2, -1];
            break; // dần 
        case 3:
        case 8:
            c = [0, 10];
            break; // tý tuất
        case 4:
        case 9:
            c = [8, -1];
            break; //  thân
    }
    return c;
}

// lôi binh 雷兵
// giáp ất bính đinh mậu kỷ canh tân nhâm quý
// thìn, dần, tuất, thân, ngọ, thìn, dần, tuất, thân, ngọ
// kị tu thuyền
function thoiLoiBinh(can) {
    var c;
    switch (can) {
        case 0:
        case 5:
            c = [4, -1];
            break; // thìn
        case 1:
        case 6:
            c = [2, -1];
            break; // dần 
        case 2:
        case 7:
            c = [10, -1];
            break; // tuất
        case 3:
        case 8:
            c = [8, -1];
            break; //  thân
        case 4:
        case 9:
            c = [6, -1];
            break; // ngọ 
    }
    return c;
}

// thiên tặc 天賊: 
// giáp ất bính đinh mậu kỷ canh tân nhâm quý
// thân, thân, dần, dần, dậu, dậu, mão, mão, dần tỵ, dần
// kị khởi tạo, động thổ, thụ tạo, thượng quan, nhập trạch, an táng, giao dịch, khai thương khố, khai thị
function thoiThienTac(can) {
    var c;
    switch (can) {
        case 0:
        case 1:
            c = [8, -1];
            break; // thân
        case 2:
        case 3:
        case 9:
            c = [2, -1];
            break; // dần 
        case 4:
        case 5:
            c = [9, -1];
            break; // dậu
        case 6:
        case 7:
            c = [3, -1];
            break; // mão
        case 8:
            c = [2, 5];
            break; // dần tỵ
    }
    return c;
}

// Cửu Xú 九醜 [ DGTNH ]
// Giờ: ất mão, ất dậu, mậu tý, mậu ngọ, kỷ mão, kỷ dậu, tân dậu, tân mão, nhâm ngọ, nhâm tý
// Kị: dựng nhà, giá thú, di chuyển, xuất quân
// kị xuất sư, giá thú, xuất hành, di tỉ, an táng
function thoiCuuXu(can, chi) {
    var k = 0;
    if (can == 1 && chi == 3) k = 1; // Ất Mão 
    else if (can == 1 && chi == 9) k = 1; // Ất Dậu
    else if (can == 4 && chi == 6) k = 1; // Mậu Ngọ 
    else if (can == 4 && chi == 0) k = 1; // Mậu Tý 
    else if (can == 5 && chi == 3) k = 1; // Kỷ Mão 
    else if (can == 5 && chi == 9) k = 1; // Kỷ Dậu
    else if (can == 7 && chi == 3) k = 1; // Tân Mão 
    else if (can == 7 && chi == 9) k = 1; // Tân Dậu
    else if (can == 8 && chi == 0) k = 1; // Nhâm Tý
    else if (can == 8 && chi == 6) k = 1; // Nhâm Ngọ 
    return k;
}

// kiếp sát 劫殺
// tý nhật tị, sửu nhật dần, dần nhật hợi, mão nhật thân, thìn nhật tị, tị nhật dần, 
// ngọ nhật hợi, mùi  nhật thân, thân nhật tị, dậu nhật dần, tuất nhật hợi, hợi nhật thân,  
// tị dần hợi thân tị dần hợi thân tị dần hợi thân
function thoiKiepSat(chi) {
    var h;
    switch (chi) {
        case 0:
        case 4:
        case 8:
            h = 5;
            break;
        case 1:
        case 5:
        case 9:
            h = 2;
            break;
        case 2:
        case 6:
        case 10:
            h = 11;
            break;
        case 3:
        case 7:
        case 11:
            h = 8;
            break;
    }
    return h;
}

// Minh Tinh Thủ Hộ thời [ DGTNH-18 ]
// chánh, thất nguyệt tòng dần thời khởi; nhị, bát nguyệt tòng thìn thời khởi; am, cửu nguyệt tòng ngọ thời khởi; 
// tứ, thập nguyệt tòng thân thời khởi; ngũ, thập nhất nguyệt tòng tuất thời khởi; lục, thập nhị nguyệt tòng tý thời khởi
function thoiMinhTinh(nguyet) {
    var m;
    switch (nguyet) {
        case 0:
        case 6:
            m = 2;
            break;
        case 1:
        case 7:
            m = 4;
            break;
        case 2:
        case 8:
            m = 6;
            break;
        case 3:
        case 9:
            m = 8;
            break;
        case 4:
        case 10:
            m = 10;
            break;
        case 5:
        case 11:
            m = 0;
            break;
    }
    return m;
}

// Thời Tam Sát Mệnh
function thoi3SatMenh(chi) {
    var m = [];
    switch (chi) {
        case 1:
            m = [2, 6, 10];
            break; // ngày và giờ Sửu kị mệnh Dần, Ngọ, Tuất
        case 4:
            m = [5, 9, 1];
            break; // ngày và giờ Thìn kị mệnh Tỵ, Dậu, Sửu
        case 7:
            m = [8, 0, 4];
            break; // ngày và giờ Mùi kị mệnh Thân, Tý, Thìn
        case 10:
            m = [11, 3, 7];
            break; // ngày và giờ Tuất kị mệnh Hợi, Mão, Mùi
    }
    return m;
}

/*
甲羊刃在卯, 乙羊刃在寅, 丙戊羊刃在午, 丁己羊刃在巳,庚羊刃在酉, 辛羊刃在申, 壬羊刃在子, 癸羊刃在亥.查法: 以日干为主, 四支见之者为是.

论十恶大败日：
　　口诀：甲辰乙巳与壬申，丙申丁亥及庚辰；
　　戊戌癸亥和辛巳，乙丑者来十位神；
　　邦国用兵须大忌，龙蛇出穴也难伸.
*/