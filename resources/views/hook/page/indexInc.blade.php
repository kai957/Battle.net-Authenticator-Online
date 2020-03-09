<!--suppress HtmlUnknownTarget -->
<div class="error-page" style="overflow:hidden;padding-top: 20px;">
    <br>
    <div class="data-container type-table">
        <table>
            <thead>
            <tr class="">
                <th align="left" style="width: 25%;"><span class="arrow">
                                            时间</span>
                </th>
                <th align="left"><span class="arrow">
                                            详细信息
                                        </span></th>
                </th>
            </tr>
            </thead>
            <tbody>
            @for($i=count($hookLog)-1;$i>=0;$i--)
                <tr class="parent-row">
                    <td class="normaltd authbianhaoa" align="left" valign="top">{{$hookLog[$i]->time}}</td>
                    <td class="normaltd authbianhaoa" align="left" valign="top">{{$hookLog[$i]->detail}}</td>
                </tr>
            @endfor
            </tbody>
        </table>
    </div>
</div>