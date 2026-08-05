<?php
class Audit {
    public static function log($userId, $event, $severity = 'INFO', $status = 'FAILED', $attemptedValue = null, $failureReason = null) {
        $conn = Database::getConnection();

        $ipAddress = $_SERVER['REMOTE_ADDR'] ?? 'UNKNOWN';
        $userAgent = $_SERVER['HTTP_USER_AGENT'] ?? 'UNKNOWN';
        $requestMethod = $_SERVER['REQUEST_METHOD'] ?? null;
        $requestUri = $_SERVER['REQUEST_URI'] ?? null;
        $sessionId = session_status() === PHP_SESSION_ACTIVE ? session_id() : null;

        $query = "INSERT INTO audit_logs (user_id, event, severity, status, ip_address, user_agent, request_method, request_uri, session_id, attempted_value, failure_reason) VALUES (:user_id, :event, :severity, :status, :ip_address, :user_agent, :request_method, :request_uri, :session_id, :attempted_value, :failure_reason)";

        $stmt = $conn->prepare($query);

        $stmt->execute([
            'user_id'        => $userId,
            'event'          => $event,
            'severity'       => $severity,
            'status'         => $status,
            'ip_address'     => substr($ipAddress, 0, 45),
            'user_agent'     => substr($userAgent, 0, 500),
            'request_method' => $requestMethod,
            'request_uri'    => $requestUri !== null ? substr($requestUri, 0, 1000) : null,
            'session_id'     => $sessionId,
            'attempted_value'=> $attemptedValue !== null ? substr($attemptedValue, 0, 255) : null,
            'failure_reason' => $failureReason !== null ? substr($failureReason, 0, 255) : null
        ]);
    }
}