/**
 * @author xsir317@gmail.com
 * @license http://creativecommons.org/licenses/by-sa/3.0/deed.zh
 */
board = function (div)
{
	var boardobj = this;
	this.gameinit = div.attr('game');
	this.chars = div.attr('chars');
	this.currgame = '';
	this.endgame = '';
	this.currcolor = 'black';
	this.currstep = 1;
	boardobj.Boardview = div;
	boardobj.Boardview.html('');
	boardobj.Boardview.mousedown(function(e){
		if(e.which == 3)
		{
			boardobj.pre();
			return false;
		}
	});
	boardobj.Boardview.bind("contextmenu", function() { return false; }); 
	//根据endgame的记录，落下后面一手棋
	this.next = function(){
		if(boardobj.endgame != boardobj.currgame)
		{
			nextstep = boardobj.endgame.substr(boardobj.currgame.length,2);
			nextstepcell = boardobj.Boardview.find('.'+nextstep);
			nextstepcell.removeClass('blank').addClass(boardobj.currcolor).html(boardobj.currstep++);
			boardobj.currcolor = (boardobj.currcolor == 'black' ? 'white':'black');
			boardobj.currgame += nextstep;
			if(boardobj.currgame == boardobj.gameinit)
			{
				boardobj.show_char();
			}
			else
			{
				boardobj.hide_char();
			}
			return true;
		}
		else
		{
			return false;
		}
	};
	//前一手
	this.pre = function(){
		if(boardobj.currgame != '')
		{
			currstep = boardobj.currgame.substr(boardobj.currgame.length-2,2);
			currstepcell = boardobj.Boardview.find('.'+currstep);
			currstepcell.removeClass('black white').addClass('blank').html('');
			boardobj.currcolor = (boardobj.currcolor == 'black' ? 'white':'black');
			boardobj.currgame = boardobj.currgame.substr(0,boardobj.currgame.length-2);
			boardobj.currstep --;
			if(boardobj.currgame == boardobj.gameinit)
			{
				boardobj.show_char();
			}
			else
			{
				boardobj.hide_char();
			}
			return true;
		}
		else
		{
			return false;
		}
	};
	//回到第一手
	this.clean = function(){
		while(boardobj.pre());
	};
	//到最后一手
	this.end = function(){
		while(boardobj.next());
	};
	
	//显示备注字符
	this.show_char = function(){
		if(!boardobj.chars) return false;
		for(var sub=0;sub< boardobj.chars.length;sub += 3)
		{
			curr = boardobj.chars.substr(sub,3);
			point = curr.substr(0,2);
			char = curr.substr(2,1);
			if(boardobj.Boardview.find('.'+point).hasClass('blank'))
			{
				boardobj.Boardview.find('.'+point).addClass('char').html(char);
			}
		}
	};
	//隐藏备注字符
	this.hide_char = function(){
		if(!boardobj.chars) return false;
		boardobj.Boardview.find(".char").removeClass('char').html('');
	};
	//根据gameinit显示整盘棋
	this.init = function(){
		boardobj.endgame = boardobj.gameinit;
		boardobj.currgame = '';
		boardobj.currcolor = 'black';
		boardobj.currstep = 1;
		boardobj.Boardview.find('.row div').removeClass('black white').addClass('blank').html('');
		boardobj.end();
	};
	//生成棋盘
	for(i=15;i>0;i--)
	{
		//insert a row
		newrow = $(document.createElement("div"));
		newrow.addClass('row');
		boardobj.Boardview.append(newrow);
		for(j=1;j<=15;j++)
		{
			//insert a cross point
			newcell = $(document.createElement("div"));
			newcell.addClass(i.toString(16) + j.toString(16));
			newcell.attr('alt',i.toString(16) + j.toString(16));
			newcell.addClass('blank');
			newrow.append(newcell);
		}
	}
	//生成控制按钮
	controlbar = $(document.createElement("div"));
	controlbar.addClass('controlbar');
	boardobj.Boardview.after(controlbar);
	nextbtn = $(document.createElement("input"));
	pre = $(document.createElement("input"));
	end = $(document.createElement("input"));
	init = $(document.createElement("input"));
	first = $(document.createElement("input"));
	first   .attr('type','button').val('|<<第一手').click(boardobj.clean).appendTo(controlbar);
	pre     .attr('type','button').val('<前一手').click(boardobj.pre).appendTo(controlbar);
	nextbtn .attr('type','button').val('后一手>').click(boardobj.next).appendTo(controlbar);
	end     .attr('type','button').val('最后>>|').click(boardobj.end).appendTo(controlbar);
	init    .attr('type','button').val('恢复').click(boardobj.init).appendTo(controlbar);
	
	boardobj.Boardview.find('.row div').click(function(){
		//落子
		if(!$(this).hasClass('blank'))
		{
			return false;
		}
		boardobj.hide_char();
		$(this).removeClass('blank').addClass(boardobj.currcolor).html(boardobj.currstep++);
		boardobj.currcolor = (boardobj.currcolor == 'black' ? 'white':'black');
		boardobj.currgame += $(this).attr('alt');
		boardobj.endgame = boardobj.currgame;
		return true;
	});
	//恢复棋盘。
	this.init();
}
$(document).ready(function(){
	$('.board_main').each(function(){
		new board($(this));
	});
});