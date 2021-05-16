/**
 @ Name：layui 表格冗余列可展开显示
 @ Author：hbm
 @ License：MIT
 @ version 1.4
 */

layui.define(['form', 'table'], function (exports) {
  //加载组件所需样式
  layui.link(layui.cache.base + '/opTable/opTable.css', function () {
  }, 'opTable');
  // 加载列配置可拖动
  layui.$("body").append('<script type="text/javascript" src="' + layui.cache.base + '/opTable/Sortable.min.js" charset="utf-8"></script>');

  var $ = layui.$
      , table = layui.table
      , VERSION = 1.4, MOD_NAME = 'opTable'
      // 展开 , 关闭
      , ON = 'on', OFF = 'off', KEY_STATUS = "status"
      // openType 0、默认效果同时只展开一项  1、点击展开多项 2、 展开全部  3、关闭全部
      , OPEN_DEF = 0, OPEN_NO_CLOSE = 1, OPEN_ALL = 2, CLOSE_ALL = 3
      // 表头展开所有图标配置key，全部item 图标配置key
      , ICON_DEF_ALL_KEY = "-1", ICON_DEF_ALL_ITEM_KEY = "0"
      // 外部接口
      , opTable = {
        index: layui.opTable ? (layui.opTable.index + 10000) : 0
        // 设置全局项
        , set: function (options) {
          var that = this;
          that.config = $.extend({}, that.config, options);
          return that;
        }
      }
      // 展开列需要需要显示的数据 数据格式为 每个页面唯一的（LAY_IINDEX）下标绑定数据  对应的数据
      , openItemData = {}
      // 表格配置项，通过表格唯一ID绑定配置详情 。
      , openTitleHelpOption = {}
      // 通过ID绑定子表实例
      , childTableObj = {}
      // 解决字表修改触发父级表格修改的问题
      , isEditListener = true
      // 持久化保存 列排序
      , opCache = {
        key: MOD_NAME,
        getVal: function (key, def) {
          return layui.data(this.key)[key] || def
        },
        setVal: function (key, val) {
          layui.data(this.key, {
            key: key
            , value: val
          });
        },
        getTableKeyId: function (tableId) {
          return window.location.pathname + tableId;
        }
      }
      , getOpenClickClass = function (elem, isAddClickClass) {
        return elem.replace("#", '').replace(".", '') + (isAddClickClass ? 'opTable-i-table-open' : '')
      }
      , getOpenAllClickClass = function (elem) {
        return getOpenClickClass(elem, true) + "-all"
      }
      // 获取展开全部图标
      , getOpenAllIcon = function (isOpenTable, elem, icon) {
        return isOpenTable ? '' : '<i class="opTable-i-table-open ' + getOpenAllClickClass(elem) + ' " ' + KEY_STATUS + '="off"  title="(展开|关闭)全部" ' + icon + '></i>'
      }
      // 操作当前实例
      , thisIns = function () {
        var that = this
            , options = that.config;
        return {
          /**
           * 重载  penTable
           * @param options layui table 参数 和opTable参数
           * @returns {thisIns}
           */
          reload: function (options) {
            options = options || {};
            that.config = $.extend(that.config, options);
            options.page = options.page === false ? false : options.page || {};

            // 一、本次重载不能为关闭分页的状态
            if (options.page) {
              that.config.page = {};
              // 2、获取到刷新前的分页属性
              var his = that.config.table.config.page || {};
              // 3、设置 起始页
              that.config.page.curr = options.page.curr || his.curr;
            }

            //  二、本次未设置条数
            if (!options.limit) {
              // 设置为刷新前的总条数
              that.config.limit = that.config.table.config.limit
            }

            that.render();
            return this;
          }
          , config: options
          // 展开全部
          , openAll: function (result) {
            if (this.isOpenAll()) {
              result && result();
              return this;
            }
            var def = that.config.openType;
            that.config.openType = OPEN_ALL;
            $("." + getOpenClickClass(that.config.elem, true)).parent().click();
            that.config.openType = def;

            $("." + getOpenAllClickClass(that.config.elem))
                .addClass("opTable-open-dow")
                .removeClass("opTable-open-up")
                .attr(KEY_STATUS, ON);

            that.config.onOpenAll && that.config.onOpenAll();
            result && result();
            return this;
          }
          // 关闭全部
          , closeAll: function () {
            if (!this.isOpenAll()) {
              return this;
            }

            var def = that.config.openType;
            that.config.openType = CLOSE_ALL;
            $("." + getOpenClickClass(that.config.elem, true)).parent().click();
            that.config.openType = def;

            $("." + getOpenAllClickClass(that.config.elem))
                .addClass("opTable-open-up")
                .removeClass("opTable-open-dow")
                .attr(KEY_STATUS, OFF);

            that.config.onCloseAll && that.config.onCloseAll();
            return this;
          }

          // 通过下标展开一项
          , openIndex: function (index) {
            var dom = $("." + getOpenClickClass(that.config.elem, true)).eq(index);
            if (dom.length <= 0) {
              return false;
            }
            var status = dom.attr(KEY_STATUS);
            if (status === ON) {
              return true;
            }
            dom.click();
            return true;
          }
          // 通过下标展开一项
          , closeIndex: function (index) {
            var dom = $("." + getOpenClickClass(that.config.elem, true)).eq(index);
            if (dom.length <= 0) {
              return false;
            }
            var status = dom.attr(KEY_STATUS);
            if (status === OFF) {
              return true;
            }
            dom.click();
            return true;
          }
          // 当前展开状态取反
          , toggleOpenIndex: function (index) {
            var dom = $("." + getOpenClickClass(that.config.elem, true)).eq(index);
            if (dom.length <= 0) {
              return false;
            }
            dom.parent().click();
            return true;
          }
          // 当前是否全部展开
          , isOpenAll: function () {
            var localTag = $("." + getOpenClickClass(that.config.elem, true));
            var isOpenAll = [];
            localTag.each(function (i) {
              if (localTag.eq(i).hasClass("opTable-open-dow")) {
                isOpenAll.push(true)
              }
            });
            // 所有项===已展开项则为全部展开
            return localTag.length === isOpenAll.length;
          }
        }
      }
      //构造器
      , Class = function (options) {
        var that = this;
        that.index = ++opTable.index;
        that.config = $.extend({}, that.config, opTable.config, options);
        that.render();
        return this;
      };

  //默认配置
  Class.prototype.config = {
    // 是否显示展开 默认显示
    openVisible: true
    // 是否支持展开全部
    , isOpenAllClick: true
    , openType: OPEN_DEF
    // 展开的item (垂直v|水平h) 排序
    , opOrientation: 'v'
    // 在那一列显示展开操作 v1.2
    , openColumnIndex: 0
    // 是否单独占一列 v1.2
    , isAloneColumn: true
    // 展开图标 {"-1":'展开全部',0:'所有item下标',1:'' ,... 配置指定下标 }
    , openIcon: {}
    // layui table引用
    , table: null
    // 展开动画执行时长
    , slideDownTime: 200
    // 关闭动画执行时长
    , slideUpTime: 100
  };

  //渲染视图
  Class.prototype.render = function () {
    var that = this
        , options = that.config
        , colArr = options.cols[0]
        , openCols = options.openCols || []
        , openNetwork = options.openNetwork || null
        , openTable = options.openTable || null
        , allBySort = []
        , colsBySort = []
        , openColsBySort = [];
    options.layuiDone = options.done || options.layuiDone;
    var singleElem = options.elem.replace("#", '').replace(".", '')

    colArr.forEach(function (it, i) {
      // 当前列属于图标列
      if (it.isOpenCol) {
        // 独占一行 移除行
        if (options.isAloneColumn) {
          colArr.splice(i, 1);
        } else {
          // 移除合并图标
          it.title = it.opDefTitle || it.title;
        }
      }
      if (it.opHelp) {
        it.title = it.title.indexOf('opTable-span-help') === -1
            ? it.title + '<span class="opTable-span-help" single="' + singleElem + it.field + '"></span>'
            : it.title;
        openTitleHelpOption[singleElem + it.field] = it.opHelp;
      }
    });
    // 下标越界问题
    options.openColumnIndex = options.openColumnIndex > colArr.length ? colArr.length : options.openColumnIndex;
    delete options["done"];

    //  表头 展开全部 图标
    var allIcon = function () {
      // 全部图标
      var icon = options.openIcon[ICON_DEF_ALL_KEY];
      if (icon) {
        return "style='background: url(" + icon + ") 0 0 no-repeat'";
      }
      return "";
    }, allItemIcon = function () {
      // 全部item图标
      var icon = options.openIcon[ICON_DEF_ALL_ITEM_KEY];
      if (icon) {
        return "style='background: url(" + icon + ") 0 0 no-repeat'";
      }
      return "";
    }, indexByIcon = function (index) {
      // 指定下标图标
      var iconPath = options.openIcon[index + ""];
      if (iconPath) {
        return "style='background: url(" + iconPath + ") 0 0 no-repeat'"
      }
      return allItemIcon;
    };


    if (options.openVisible) {
      //1、 单独占一列
      if (options.isAloneColumn) {
        //  1、在指定列 插入可展开操作
        colArr.splice(options.openColumnIndex, 0, {
          width: 50,
          isOpenCol: true,
          title: getOpenAllIcon(!options.isOpenAllClick, options.elem, allIcon()),
          templet: function (item) {
            var cla = getOpenClickClass(options.elem, false);
            // 解决页面多个表格问题
            openItemData[cla] = openItemData[cla] || {};
            openItemData[cla][item.LAY_INDEX] = item;
            return "<i class='opTable-i-table-open " + cla + "opTable-i-table-open' " + KEY_STATUS + "='off'  data='"
                //  把当前列的数据绑定到控件
                + item.LAY_INDEX
                + " ' elem='"
                + cla
                + "' title='展开' " + (indexByIcon()()) + "></i>";
          }
        });
      } else {
        //2、与数据占一列
        var openColumn = colArr[options.openColumnIndex];
        delete openColumn["edit"];
        openColumn.opDefTitle = openColumn.title;
        openColumn.isOpenCol = true;
        // 存在排序 都不支持展开全部
        openColumn.title = getOpenAllIcon(openColumn["sort"], options.elem, allIcon()) + ("<span class='opTable-span-seize'></span>") + openColumn.title;
        openColumn.templet = function (item) {
          var cla = getOpenClickClass(options.elem, false);
          openItemData[cla] = openItemData[cla] || {};
          openItemData[cla][item.LAY_INDEX] = item;
          return ("<i class='opTable-i-table-open " + cla + "opTable-i-table-open' " + KEY_STATUS + "='off'  data='"
              //  把当前列的数据绑定到控件
              + item.LAY_INDEX
              + " ' elem='"
              + cla
              + "' title='展开' "
              + indexByIcon(item.LAY_INDEX) + "></i>")
              + ("<span class='opTable-span-seize'></span>")
              + (openColumn.onDraw ? openColumn.onDraw(item) : item[openColumn.field]);
        };
      }
    }

    // 处理列排序持久化问题
    var sortCache = opCache.getVal(opCache.getTableKeyId(options.id), null);
    if (sortCache) {
      // 获取第一个 cols 是否为展开图标列
      colArr.forEach(function (item, i) {
        if (colArr[0].isOpenCol) {
          i !== 0 && allBySort.push(item);
        } else {
          allBySort.push(item)
        }
      });
      options.openCols.forEach(function (item, i) {
        item.isOpenColsItem = true;
        allBySort.push(item)
      });

      allBySort.forEach(function (item) {
        if (item.title) {
          var cache = sortCache[item.title];
          if (!cache) {
            item.isOpenColsItem ? openColsBySort.push(item) : colsBySort.push(item);
            return;
          }

          // 1、处于显示状态 则放到普通列
          if (cache.isShow) {
            item.opCacheSort = cache.sort;
            // 属性转换
            if (item.isOpenColsItem && item.onDraw) {
              item.templet = item.onDraw;
              delete item['onDraw'];
            }
            colsBySort.push(item);
          } else {
            // 2、放到展开列
            item.opCacheSort = cache.sort;
            // 属性转换
            if (!item.isOpenColsItem && item.templet) {
              item.onDraw = item.templet;
              delete item['templet'];
            }
            openColsBySort.push(item);
          }
        } else {
          // 3、没有唯一标识（field）的列，让他们回到自己的数组里
          item.isOpenColsItem ? openColsBySort.push(item) : colsBySort.push(item);
        }
      });

      colsBySort.sort(function (a, b) {
        return parseInt(a.opCacheSort) - parseInt(b.opCacheSort);
      });

      openColsBySort.sort(function (a, b) {
        return parseInt(a.opCacheSort) - parseInt(b.opCacheSort);
      });

      if (colArr[0].isOpenCol) {
        colsBySort.splice(options.openColumnIndex, 0, colArr[0]);
      }

      // 重新赋值持久化内容
      options.cols[0] = colsBySort;
      options.openCols = openColsBySort;
    }

    //  2、表格Render
    options.table = table.render(
        $.extend({
          done: function (res, curr, count) {
            initExpandedListener();
            options.layuiDone && options.layuiDone(res, curr, count)
          }
        }, options));

    childTableObj[singleElem] = options;

    // 3、展开事件
    function initExpandedListener() {
      initHelpListener();
      if (!options.openVisible) {
        return;
      }

      var openIconDom = $("." + getOpenClickClass(options.elem, true))
          .parent()
          .unbind("click")
          .click(function (e) {
            layui.stope(e);
            var that = $(this).children()
                , _this = this
                , optId = that.attr("elem")
                , itemIndex = parseInt(that.attr("data"))
                , bindOpenData = openItemData[optId][itemIndex]
                , status = that.attr(KEY_STATUS) === 'on'
                // 操作倒三角
                , dowDom = that.parent().parent().parent().parent().find(".opTable-open-dow")
                // 展开的tr
                , addTD = that.parent().parent().parent().parent().find(".opTable-open-td")
                // 行点击Class
                , itemClickClass = optId + '-opTable-open-item-div'
                , options = childTableObj[optId].config || childTableObj[optId]
                , openCols = options.openCols
                , colArr = options.cols[0]
                // 子表ID
                , childTableId;

            function initOnClose() {
              options.onClose && options.onClose(bindOpenData, itemIndex);
            }

            // 关闭全部
            if (options.openType === CLOSE_ALL) {
              dowDom
                  .addClass("opTable-open-up")
                  .removeClass("opTable-open-dow")
                  .attr(KEY_STATUS, OFF);
              addTD.slideUp(options.slideUpTime, function () {
                addTD.remove();
              });
              //关闭回调
              initOnClose();
              return;
            }

            // 展开全部
            if (options.openType === OPEN_ALL) {
              dowDom
                  .addClass("opTable-open-dow")
                  .removeClass("opTable-open-up")
                  .attr(KEY_STATUS, ON);
              if (status) {
                _this.addTR.remove();
              }
            } else if (options.openType === OPEN_DEF) {
              // 关闭类型
              var sta = dowDom.attr(KEY_STATUS),
                  isThis = (that.attr("data") === dowDom.attr("data"));
              //1、关闭展开的
              dowDom
                  .addClass("opTable-open-up")
                  .removeClass("opTable-open-dow")
                  .attr(KEY_STATUS, OFF);

              //2、如果当前 = 展开 && 不等于当前的 关闭
              if (sta === ON && isThis) {
                addTD.slideUp(options.slideUpTime, function () {
                  addTD.remove();
                  initOnClose();
                });
                return;
              } else {
                that.attr(KEY_STATUS, OFF);
                addTD.remove();
              }
            } else if (options.openType === OPEN_NO_CLOSE) {
              //  1、如果当前为打开，再次点击则关闭
              if (status) {
                that.removeClass("opTable-open-dow");
                that.attr(KEY_STATUS, OFF);
                this.addTR.find("div").eq(0).slideUp(options.slideUpTime, function () {
                  _this.addTR.remove();
                  //关闭回调
                  initOnClose();
                });
                return;
              }

            }

            // 把添加的 tr 绑定到当前 移除时使用 独占一列时 将表格列分配+1
            this.addTR = $([
              "<tr><td class='opTable-open-td'  colspan='" + (colArr.length + (options.isAloneColumn ? 1 : 0)) + "'>"
              , "<div style='margin-left: 50px;display: none'></div>"
              , "</td></tr>"].join("")
            );

            // 所有内容的主容器
            var divContent =
                _this.addTR
                    .children()
                    .children();

            that.parent().parent().parent().after(this.addTR);

            var html = [];

            // 1、从网络获取
            if (openNetwork) {
              loadNetwork();
            }
            // 2、展开显示表格
            else if (openTable) {
              if (typeof openTable !== "function") {
                throw  "OPTable: openTable attribute is function ";
              }
              var tableOptions = openTable(bindOpenData);
              tableOptions.layuiDone = tableOptions.done;
              tableOptions.done = function (res, curr, count) {
                // 子表行聚焦向上传递问题
                $(".opTable-open-td tr").hover(function (e) {
                  layui.stope(e)
                });
                // 子表排序
                tableOptions.layuiDone && tableOptions.layuiDone(res, curr, count);
              };

              childTableId = tableOptions.elem.replace("#", '').replace(".", '');

              divContent
                  .empty()
                  .append("<table id='" + childTableId + "' lay-filter='" + childTableId + "'></table>")
                  .css({
                    "padding": "0 10px 0 50px", "margin-left": "0", "width":
                        _this.addTR.width()
                  })
                  .fadeIn(400);

              // 设置展开表格颜色为浅色背景
              addTD.css("cssText", "background-color:#FCFCFC!important");
              childTableObj[childTableId] = layui.opTable.render(tableOptions);
            }
            // 3、从左到右依次排列 Item 默认风格
            else {
              openCols.forEach(function (val) {
                appendItem(val, bindOpenData);
              });
              divContent.append(html.join(''));
              this.addTR.find("div").slideDown(options.slideDownTime);
              bindBlur(bindOpenData);
            }

            function loadNetwork() {
              divContent.empty()
                  .append('<div class="opTable-network-message" ><i class="layui-icon layui-icon-loading layui-icon layui-anim layui-anim-rotate layui-anim-loop" data-anim="layui-anim-rotate layui-anim-loop"></i></div>');
              _this.addTR.find("div").slideDown(options.slideDownTime);

              openNetwork.onNetwork(bindOpenData
                  //加载成功
                  , function (obj) {
                    //  2、从左到右依次排列 Item
                    openNetwork.openCols.forEach(function (val, index) {
                      appendItem(val, obj);
                    });
                    // 填充展开数据
                    divContent.empty().append(html.join(''));
                    bindBlur(obj);
                  }
                  , function (msg) {
                    divContent.empty()
                        .append("<div class='opTable-reload opTable-network-message' style='text-align: center;margin-top: 20px'>" + (msg || "没有数据") + "</div>")

                    $(".opTable-reload")
                        .unbind()
                        .click(function (e) {
                          loadNetwork();
                        });
                  })
            }

            /**
             * 添加默认排版风格 item
             * @param colsItem  cols配置信息
             * @param openData  展开数据
             */
            function appendItem(colsItem, openData) {
              // 显示帮助图标
              if (colsItem.opHelp) {
                colsItem.title = colsItem.title.indexOf('opTable-span-help') === -1
                    ? colsItem.title + '<span class="opTable-span-help" single="' + singleElem + colsItem.field + '"></span>'
                    : colsItem.title;
                openTitleHelpOption[singleElem + colsItem.field] = colsItem.opHelp;
              }

              //  1、自定义模板
              if (colsItem.templet) {
                html.push("<div id='" + colsItem.field + "' class='opTable-open-item-div " + itemClickClass + "' index='" + itemIndex + "' opOrientation='" + options.opOrientation + "'>")
                html.push(colsItem.templet(openData));
                html.push("</div>")
                //  2、可下拉选择类型
              } else if (colsItem.type && colsItem.type === 'select') {
                var child = ["<div id='" + colsItem.field + "' class='opTable-open-item-div " + itemClickClass + "' index='" + itemIndex + "' opOrientation='" + options.opOrientation + "' >"];
                child.push("<span style='color: #99a9bf'>" + colsItem["title"] + "：</span>");
                child.push("<div class='layui-input-inline'><select  lay-filter='" + colsItem.field + "'>");
                colsItem.items(openData).forEach(function (it) {
                  child.push("<option value='" + it.id + "' ");
                  child.push(it.isSelect ? " selected='selected' " : "");
                  child.push(" >" + it.value + "</option>");
                });
                child.push("</select></div>");
                child.push("</div>");
                html.push(child.join(""));
                setTimeout(function () {
                  layui.form.render();
                  //  监听 select 修改
                  layui.form.on('select(' + colsItem.field + ')', function (data) {
                    if (options.onEdit && colsItem.isEdit && colsItem.isEdit(data, openData)) {
                      var json = {};
                      json.value = data.value;
                      json.field = colsItem.field;
                      openData[colsItem.field] = data.value;
                      json.data = JSON.parse(JSON.stringify(openData));
                      options.onEdit(json);
                    }
                  });
                }, 20);
              } else {
                var text = colsItem.onDraw ? colsItem.onDraw(openData) : openData[colsItem["field"]];
                // 处理null字符串问题
                text = text || "";
                // 3、默认类型
                html.push("<div id='" + colsItem.field + "' class='opTable-open-item-div " + itemClickClass + "' index='" + itemIndex + "' opOrientation='" + options.opOrientation + "'>");
                html.push("<span class='opTable-item-title'>" + colsItem["title"] + "：</span>");
                html.push((colsItem.edit ?
                        ("<input  class='opTable-exp-value opTable-exp-value-edit' autocomplete='off' name='" + colsItem["field"] + "' value='" + text + "'/>")
                        : ("<span class='opTable-exp-value' >" + text + "</span>")
                ));
                html.push("</div>");
              }

            }

            /**
             * 绑定监听 修改失焦点监听
             * @param bindOpenData
             */
            function bindBlur(bindOpenData) {
              $(".opTable-exp-value-edit")
                  .unbind("blur")
                  .blur(function () {
                    var that = $(this), name = that.attr("name"), val = that.val();
                    // 设置了回调 &&发生了修改
                    if (options.onEdit && bindOpenData[name] + "" !== val) {
                      var json = {};
                      json.value = that.val();
                      json.field = that.attr("name");
                      bindOpenData[name] = val;
                      json.data = bindOpenData;
                      options.onEdit(json);
                    }
                  })
                  .keypress(function (even) {
                    even.which === 13 && $(this).blur()
                  })
            }

            if (options.onItemClick) {
              $("." + itemClickClass)
                  .unbind("click")
                  .click(function (e) {
                    var field = $(this).attr("id");
                    // 根据下标获取行数据
                    var lineData = openItemData[getOpenClickClass(options.elem, false)][$(this).attr("index")];
                    options.onItemClick({
                      lineData: lineData,
                      field: field,
                      value: lineData[field],
                      div: $(this),
                      e: e
                    });
                  });
            }

            that.addClass("opTable-open-dow");
            that.attr(KEY_STATUS, ON);

            // 创建成功回调
            options.onInitSuccess && options.onInitSuccess(bindOpenData, itemIndex, this.addTR, childTableObj[childTableId]);
            setTimeout(function () {
              // 展开回调
              options.onOpen && options.onOpen(bindOpenData, itemIndex, that.addTR, childTableObj[childTableId]);
              initHelpListener();
            }, options.slideDownTime);

          });


      // 列显示配置
      openIconDom.parents('.layui-table-view')
          .find('.layui-table-tool>.layui-table-tool-self > div[lay-event="LAYTABLE_COLS"]')
          .eq(0)
          .unbind("click")
          .attr("elem", singleElem)
          .on('click', function () {
            var dom = $(this);
            setTimeout(function () {
              var ul = dom.find("ul"), localOpt = childTableObj[dom.attr("elem")];
              if (!localOpt.openCols) {
                return;
              }
              var html = [];
              localOpt.cols[0].forEach(function (item) {
                if (item.title && item.toolbar) {
                  html.push('<li>');
                  html.push('<input type="checkbox" name="' + item.title + '" title="' + item.title + '" checked="checked" lay-skin="primary"/>');
                  html.push('</li>');
                }
              });
              localOpt.openCols.forEach(function (item) {
                if (item.title) {
                  // 获取文本
                  var title = $("<div>" + item.title + "</div>").text();
                  html.push('<li>');
                  html.push('<i class="opTable-i-table-open"></i>');
                  html.push("<div style='display: inline-block;margin-left: 12px'>");
                  html.push('<input type="checkbox" name="' + title + '" title="' + title + '" lay-skin="primary"/>');
                  html.push("</div>");
                  html.push('</li>');
                }
              });

              ul.append(html.join(""));
              ul.append(
                  '<div class="op-edit-field-btn">'
                  + '<button type="button" class="layui-btn layui-btn-xs layui-btn-warm">重置</button>'
                  + '<button type="button" class="layui-btn layui-btn-xs layui-btn-normal">保存</button>'
                  + '</div>'
              );
              layui.form.render('checkbox');
              Sortable.create(ul[0]);

              ul.find('.op-edit-field-btn button').unbind("click").click(function () {
                var singKey = opCache.getTableKeyId(localOpt.id);
                if ($(this).text() === '重置') {
                  opCache.setVal(singKey, []);
                  layer.msg("成功，刷新生效。");
                } else {
                  var inputs = ul.find("input");
                  var sort = {};
                  inputs.each(function (i) {
                    var item = inputs.eq(i);
                    sort[item.attr("title")] = {
                      sort: i,
                      isShow: item.parent().find(".layui-form-checked").length > 0
                    };
                  });

                  opCache.setVal(singKey, sort);
                  layer.msg("成功，刷新生效。");
                }
              })

            }, 40);
          });

      // (展开|关闭)全部
      $("." + getOpenAllClickClass(options.elem))
          .parent()
          .parent()
          .unbind("click")
          .click(function () {
            if (!options.isOpenAllClick) {
              return
            }
            var tag = $(this).find("i").eq(0), status = tag.attr(KEY_STATUS);
            if (status === ON) {
              tag.addClass("opTable-open-up")
                  .removeClass("opTable-open-dow")
                  .attr(KEY_STATUS, OFF);
              options.thisIns.closeAll();
            } else {
              tag
                  .addClass("opTable-open-dow")
                  .removeClass("opTable-open-up")
                  .attr(KEY_STATUS, ON);
              options.thisIns.openAll();
            }

          });
    }

    // 弹出帮助提示
    function initHelpListener() {
      $(".opTable-span-help").unbind("click").click(function (e) {
        layui.stope(e)
        var that = $(this);
        var opt = openTitleHelpOption[that.attr("single")];
        var index = layer.tips(opt.text + "<span class='op-span-help-close'>关闭</span>", that.parent(), $.extend({
          tips: 3, time: 40000, success: function () {
            $(".op-span-help-close").click(function () {
              layer.close(index);
            })
          }
        }, opt.tipOpt))
      });

    }

    //  4、监听排序事件
    var elem = $(options.elem).attr("lay-filter");

    //  5、监听表格排序
    table.on('sort(' + elem + ')', function (obj) {
      options.onSort && options.onSort(obj);
      // 重新绑定事件
      initExpandedListener();
    });

    //  6、单元格编辑
    layui.table.on('edit(' + elem + ')', function (obj) {
      if (!isEditListener) {
        return;
      }
      isEditListener = false;
      options.onEdit && options.onEdit(obj);
      setTimeout(function () {
        isEditListener = true;
      }, 100);
    });

  };

  //核心入口
  opTable.render = function (options) {
    var ins = new Class(options);
    var ex = thisIns.call(ins);
    ins.config.thisIns = ex;
    return ex;
  };

  opTable.openTableVersion = VERSION;
  opTable.childTableObj = childTableObj;
  exports(MOD_NAME, opTable);
});
