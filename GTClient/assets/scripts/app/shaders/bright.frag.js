var e = module,
    o = exports;
e.exports =
    "\n#ifdef GL_ES\nprecision mediump float;\n#endif\n\nvarying vec2 v_texCoord;\nuniform float sys_time;\nuniform float width;     //流光的宽度范围 (调整该值改变流光的宽度)\nuniform float strength;  //流光增亮强度   (调整该值改变流光的增亮强度)\nuniform float offset;    //偏移值         (调整该值改变流光的倾斜程度)\n\nvoid main()\n{\n    vec4 src_color = texture2D(CC_Texture0, v_texCoord).rgba;\n\n    if( v_texCoord.x < (sys_time - offset * v_texCoord.y) &&  v_texCoord.x > (sys_time - offset * v_texCoord.y - width))\n    {\n        vec3 improve = strength * vec3(255, 255, 255);\n        vec3 result = improve * vec3( src_color.r, src_color.g, src_color.b);\n        gl_FragColor = vec4(result, src_color.a);\n\n    }else{\n        gl_FragColor = src_color;\n    }\n}\n";
