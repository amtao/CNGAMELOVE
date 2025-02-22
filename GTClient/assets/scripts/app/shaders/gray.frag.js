var e = module,
    o = exports;
e.exports =
    "\n#ifdef GL_ES\nprecision lowp float;\n#endif\n\nvarying vec4 v_fragmentColor;\nvarying vec2 v_texCoord;\nvoid main()\n{\n    vec4 c = v_fragmentColor * texture2D(CC_Texture0, v_texCoord);\n    gl_FragColor.xyz = vec3(0.2126*c.r + 0.7152*c.g + 0.0722*c.b);\n    gl_FragColor.w = c.w;\n}\n";
