import 'dart:convert';
import 'package:grad_project/core/constants/app_config.dart';
import 'package:grad_project/core/storage/local_storage.dart';
import 'package:http/http.dart' as http;

/// ApiResponse
/// ---------------------------
/// نموذج موحّد لإرجاع نتيجة الطلب:
/// success: هل العملية ناجحة؟
/// message: نص من الباك اند
/// data: أي بيانات إضافية
class ApiResponse {
  final bool success;
  final String message;
  final dynamic data;

  ApiResponse({
    required this.success,
    required this.message,
    this.data,
  });

  factory ApiResponse.fromJson(Map<String, dynamic> json) {
    return ApiResponse(
      success: json["status"] == "success",
      message: json["message"] ?? "",
      data: json["data"],
    );
  }
}

/// ApiService
/// ---------------------------
/// مسؤول عن طلبات GET و POST.
class ApiService {
  static const _authorizationHeaderName = 'Authorization';
  static const _authorizationScheme = 'Bearer';

  static String _authorizationHeaderValue(String token) =>
      '$_authorizationScheme $token';

  /// POST Request
  static Future<ApiResponse> post(
    String path, {
    Map<String, dynamic>? body,
  }) async {
    final uri = AppConfig.apiUri(path);
    final token = await LocalStorage.getToken();

    final response = await http.post(
      uri,
      headers: {
        "Accept": "application/json",
        "Content-Type": "application/json",
        if (token != null && token.isNotEmpty)
          _authorizationHeaderName: _authorizationHeaderValue(token),
      },
      body: jsonEncode(body),
    );

    if (response.statusCode == 401) {
      await LocalStorage.clear();
      throw Exception("Unauthorized");
    }

    return ApiResponse.fromJson(json.decode(response.body));
  }

  /// GET Request
  static Future<ApiResponse> get(String path) async {
    final uri = AppConfig.apiUri(path);
    final token = await LocalStorage.getToken();

    final response = await http.get(
      uri,
      headers: {
        "Accept": "application/json",
        if (token != null && token.isNotEmpty)
          _authorizationHeaderName: _authorizationHeaderValue(token),
      },
    );

    final newToken = response.headers['x-new-token'];
    if (newToken != null && newToken.isNotEmpty) {
      await LocalStorage.saveToken(newToken);
    }

    if (response.statusCode == 401) {
      await LocalStorage.clear();
      throw Exception("Unauthorized");
    }

    return ApiResponse.fromJson(json.decode(response.body));
  }
}
